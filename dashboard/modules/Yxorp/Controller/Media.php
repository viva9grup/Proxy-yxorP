<?php
/**
 * This file is part of the yxorP project.
 *
 * (c) Artur Heinze - 🅰🅶🅴🅽🆃🅴🅹🅾, http://agentejo.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace yxorP\Controller;

use DirectoryIterator;
use JetBrains\PhpStorm\ArrayShape;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SVGSanitizer;
use yxorP\AuthController;
use ZipArchive;
use ZipStream\Exception\FileNotFoundException;
use ZipStream\Exception\FileNotReadableException;
use ZipStream\ZipStream;
use function file_get_contents;
use function preg_match;
use function session_write_close;

/**
 * @property mixed|null $memory
 */
class Media extends AuthController
{

    protected $root;

    public function api(): bool
    {

        $cmd = $this->param('cmd', false);
        $mediapath = $this->module('yxorp')->getGroupVar('finder.path', '');

        if (!$mediapath && !$this->module('yxorp')->isSuperAdmin()) {
            $this->root = rtrim($this->app->path("#uploads:"), '/');
        } else {
            $this->root = YXORP_DIR == YXORP_ENV_ROOT ? rtrim($this->app->path("site:{$mediapath}"), '/') : YXORP_ENV_ROOT;
        }

        if (file_exists($this->root) && in_array($cmd, get_class_methods($this))) {

            $this->app->response->mime = 'json';

            return $this->{$cmd}();
        }

        return false;
    }

    public function savebookmarks(): bool|string
    {

        if ($bookmarks = $this->param('bookmarks', false)) {
            $this->memory->set('mediamanager.bookmarks.' . $this->user['_id'], $bookmarks);
        }

        return json_encode($bookmarks);
    }

    public function loadbookmarks(): bool|string
    {

        return json_encode($this->app->memory->get('mediamanager.bookmarks.' . $this->user['_id'], ['folders' => [], 'files' => []]));
    }

    #[ArrayShape(['folders' => "array", 'files' => "array", 'path' => "string"])] protected function ls(): array
    {

        $data = ['folders' => [], 'files' => []];
        $toignore = [
            '.svn', '_svn', 'cvs', '_darcs', '.arch-params', '.monotone', '.bzr', '.git', '.hg',
            '.ds_store', '.thumb', '.idea'
        ];

        $cpfolder = $this->app->path('#root:');
        $sitefolder = $this->app->path('site:');
        $isSuperAdmin = $this->module('yxorp')->isSuperAdmin();

        if ($path = $this->_getPathParameter()) {

            $dir = $this->root . '/' . trim($path, '/');
            $data['path'] = $dir;

            if (file_exists($dir)) {

                foreach (new DirectoryIterator($dir) as $file) {

                    if ($file->isDot()) continue;
                    if ($file->isDir() && $file->getRealPath() == $cpfolder && !$isSuperAdmin) continue;

                    $filename = $file->getFilename();

                    if ($filename[0] == '.' && in_array(strtolower($filename), $toignore)) continue;

                    $isDir = $file->isDir();

                    $data[$file->isDir() ? 'folders' : 'files'][] = [
                        'is_file' => !$isDir,
                        'is_dir' => $isDir,
                        'is_writable' => is_writable($file->getPathname()),
                        'name' => $filename,
                        'path' => trim($path . '/' . $file->getFilename(), '/'),
                        'rel_site_path' => trim(str_replace($sitefolder, '', $file->getPathname()), '/'),
                        'url' => $this->app->pathToUrl($file->getPathname()),
                        'size' => $isDir ? '' : $this->app->helper('utils')->formatSize($file->getSize()),
                        'filesize' => $isDir ? '' : $file->getSize(),
                        'ext' => $isDir ? '' : strtolower($file->getExtension()),
                        'lastmodified' => $file->isDir() ? '' : date('d.m.y H:i', $file->getMTime()),
                        'modified' => $file->isDir() ? '' : $file->getMTime(),
                    ];
                }
            }
        }

        return $data;
    }

    protected function _getPathParameter()
    {

        $path = $this->param('path', false);

        if ($path) {

            $path = trim($path);

            if (str_contains($path, '../')) {
                $path = false;
            }
        }

        return $path;
    }

    protected function upload(): bool|string
    {

        session_write_close();

        $path = $this->_getPathParameter();

        if (!$path) return false;

        $files = $_FILES['files'] ?? [];
        $targetpath = $this->root . '/' . trim($path, '/');
        $uploaded = [];
        $failed = [];

        // absolute paths for hook
        $_uploaded = [];
        $_failed = [];

        if (isset($files['name']) && file_exists($targetpath)) {

            for ($i = 0; $i < count($files['name']); $i++) {

                // clean filename
                $clean = preg_replace('/[^a-zA-Z0-9-_.]/', '', str_replace(' ', '-', $files['name'][$i]));
                $_file = $targetpath . '/' . $clean;

                if (!$files['error'][$i] && $this->_isFileTypeAllowed($clean) && move_uploaded_file($files['tmp_name'][$i], $_file)) {
                    $uploaded[] = $files['name'][$i];
                    $_uploaded[] = $_file;

                    if (preg_match('/\.(svg|xml)$/i', $clean)) {
                        file_put_contents($_file, SVGSanitizer::clean(file_get_contents($_file)));
                    }

                } else {
                    $failed[] = ['file' => $files['name'][$i], 'error' => $files['error'][$i]];
                    $_failed[] = $_file;
                }
            }
        }

        $this->app->trigger('yxorp.media.upload', [$_uploaded, $_failed]);

        return json_encode(['uploaded' => $uploaded, 'failed' => $failed]);
    }

    protected function _isFileTypeAllowed($file): bool|int
    {

        $allowed = trim($this->module('yxorp')->getGroupVar('finder.allowed_uploads', $this->app->retrieve('allowed_uploads', '*')));

        if (strtolower(pathinfo($file, PATHINFO_EXTENSION)) == 'php' && !$this->module('yxorp')->isSuperAdmin()) {
            return false;
        }

        if ($allowed == '*') {
            return true;
        }

        $allowed = str_replace([' ', ','], ['', '|'], preg_quote($allowed));

        return preg_match("/\.({$allowed})$/i", $file);
    }

    protected function uploadfolder(): bool|string
    {

        session_write_close();

        $path = $this->_getPathParameter();

        if (!$path) return false;

        $files = $_FILES['files'] ?? [];
        $paths = $this->param('paths') ?? [];
        $targetpath = $this->root . '/' . trim($path, '/');
        $uploaded = [];
        $failed = [];

        // absolute paths for hook
        $_uploaded = [];
        $_failed = [];

        if (isset($files['name']) && file_exists($targetpath)) {

            for ($i = 0; $i < count($files['name']); $i++) {

                $_path = str_replace('\\', '/', dirname(strip_tags($paths[$i])));

                // clean filename
                $clean = preg_replace('/[^a-zA-Z0-9-_.]/', '', str_replace(' ', '-', $files['name'][$i]));
                $_file = $targetpath . '/' . $_path . '/' . $clean;

                if (!is_dir(dirname($_file))) {
                    mkdir(dirname($_file), 0777, true);
                }

                if (!$files['error'][$i] && $this->_isFileTypeAllowed($clean) && move_uploaded_file($files['tmp_name'][$i], $_file)) {
                    $uploaded[] = $files['name'][$i];
                    $_uploaded[] = $_file;

                    if (preg_match('/\.(svg|xml)$/i', $clean)) {
                        file_put_contents($_file, SVGSanitizer::clean(file_get_contents($_file)));
                    }

                } else {
                    $failed[] = ['file' => $files['name'][$i], 'error' => $files['error'][$i]];
                    $_failed[] = $_file;
                }
            }
        }

        $this->app->trigger('yxorp.media.upload', [$_uploaded, $_failed]);

        return json_encode(['uploaded' => $uploaded, 'failed' => $failed]);
    }

    protected function createfolder(): bool|string
    {

        $path = $this->_getPathParameter();

        if (!$path) return false;

        $name = $this->param('name', false);
        $ret = false;

        if ($name) {
            $ret = mkdir($this->root . '/' . trim($path, '/') . '/' . $name);
        }

        return json_encode(['success' => $ret]);
    }

    protected function createfile(): bool|string
    {

        $path = $this->_getPathParameter();

        if (!$path) return false;

        $name = $this->param('name', false);
        $ret = false;

        if ($name && $this->_isFileTypeAllowed($name)) {
            $ret = @file_put_contents($this->root . '/' . trim($path, '/') . '/' . $name, '');
        }

        return json_encode(['success' => $ret]);
    }

    protected function removefiles(): bool|string
    {

        $paths = (array)$this->param('paths', []);
        $deletions = [];

        foreach ($paths as $path) {

            $delpath = $this->root . '/' . trim($path, '/');

            if (is_dir($delpath)) {
                $this->_rrmdir($delpath);
            }

            if (is_file($delpath)) {
                unlink($delpath);
            }

            $deletions[] = $delpath;
        }

        $this->app->trigger('yxorp.media.removefiles', [$deletions]);

        return json_encode(["success" => true]);
    }

    protected function _rrmdir($dir)
    {

        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir . "/" . $object) == "dir") $this->_rrmdir($dir . "/" . $object); else unlink($dir . "/" . $object);
                }
            }

            reset($objects);
            rmdir($dir);
        }
    }

    protected function rename(): bool|string
    {

        $path = $this->_getPathParameter();

        if (!$path) return false;

        $name = $this->param('name', false);

        if ($name && $this->_isFileTypeAllowed($name)) {
            $source = $this->root . '/' . trim($path, '/');
            $target = dirname($source) . '/' . $name;

            rename($source, $target);
            $this->app->trigger('yxorp.media.rename', [$source, $target]);
        }

        return json_encode(["success" => true]);
    }

    protected function readfile()
    {

        $path = $this->_getPathParameter();

        if (!$path) return false;

        $file = $this->root . '/' . trim($path, '/');

        if (file_exists($file)) {
            echo file_get_contents($file);
        }

        $this->app->stop();
    }

    protected function writefile(): bool|string
    {

        $path = $this->_getPathParameter();

        if (!$path) return false;

        $content = $this->param('content', false);
        $file = $this->root . '/' . trim($path, '/');
        $ret = false;

        if (file_exists($file) && $content !== false) {
            $ret = file_put_contents($file, $content);
        }

        return json_encode(['success' => $ret]);
    }

    protected function unzip(): bool|string
    {

        session_write_close(); // improve concurrency loading

        $path = $this->_getPathParameter();

        if (!$path) return false;

        $return = ['success' => false];
        $zip = $this->param('zip', false);

        if ($zip) {

            $path = $this->root . '/' . trim($path, '/');
            $zip = $this->root . '/' . trim($zip, '/');

            $za = new ZipArchive;

            if ($za->open($zip)) {

                if ($za->extractTo($path)) {
                    $return = ['success' => true];
                }

                $za->close();
            }
        }

        return json_encode($return);
    }

    protected function download()
    {

        $path = $this->_getPathParameter();

        if (!$path) return false;

        $file = $this->root . '/' . trim($path, '/');

        if (!$path && !file_exists($file)) {
            $this->app->stop();
        }

        if (is_dir($file)) {
            return $this->downloadfolder();
        }

        $pathinfo = $path_parts = pathinfo($file);

        header('Pragma: public');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Cache-Control: private', false);
        header('Content-Type: application/force-download');
        header('Content-Disposition: attachment; filename="' . $pathinfo["basename"] . '";');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . filesize($file));

        //readfile($file);

        $handle = fopen($file, 'rb');

        while (!feof($handle)) {
            echo fread($handle, 1000);
        }

        fclose($handle);

        $this->app->stop();
    }

    protected function downloadfolder()
    {

        session_write_close(); // improve concurrency loading

        $path = $this->_getPathParameter();

        if (!$path) return false;

        $folder = $this->root . '/' . trim($path, '/');

        if (!$path && !file_exists($folder)) {
            $this->app->stop();
        }

        header('X-Accel-Buffering: no');

        $prefix = basename($path);
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($folder), RecursiveIteratorIterator::LEAVES_ONLY);
        $zip = new ZipStream("{$prefix}.zip");

        foreach ($files as $name => $file) {

            if ($file->isDir()) continue;

            $filePath = $file->getRealPath();
            $relativePath = substr($filePath, strlen($folder) + 1);
            try {
                $zip->addFileFromPath("{$prefix}/{$relativePath}", $filePath);
            } catch (FileNotFoundException $e) {
            } catch (FileNotReadableException $e) {
            }
        }

        $zip->finish();

        $this->app->stop();
    }

    protected function getfilelist(): bool|string
    {

        session_write_close(); // improve concurrency loading

        $list = [];
        $toignore = [
            '\.svn', '_svn', 'cvs', '_darcs', '\.arch-params', '\.monotone', '\.bzr', '\.git', '\.hg', '\.ds_store', '\.thumb', '\/cache'
        ];

        $toignore = '/(' . implode('|', $toignore) . ')/i';

        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->root)) as $file) {

            if ($file->isDir()) continue;

            $filename = $file->getFilename();

            if ($filename[0] == '.' || preg_match($toignore, $file->getPathname())) continue;

            $path = trim(str_replace(['\\', $this->root], ['/', ''], $file->getPathname()), '/');

            $list[] = [
                "is_file" => true,
                "is_dir" => false,
                "is_writable" => is_writable($file->getPathname()),
                "name" => $filename,
                "path" => $path,
                "dir" => dirname($path),
                "url" => $this->app->pathToUrl($file->getPathname()),
            ];
        }

        return json_encode($list);
    }

}