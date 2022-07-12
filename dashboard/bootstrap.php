<?php
/**
 * This file is part of the yxorP project.
 *
 * (c) Artur Heinze - 🅰🅶🅴🅽🆃🅴🅹🅾, http://agentejo.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * yxorP start time
 */
define('YXORP_START_TIME', microtime(true));

if (!defined('YXORP_CLI')) {
    define('YXORP_CLI', PHP_SAPI == 'cli');
}

// Autoload vendor libs
include(__DIR__ . '/lib/vendor/autoload.php');

// include core classes for better performance
if (!class_exists('Lime\\App')) {
    include(__DIR__ . '/lib/Lime/App.php');
    include(__DIR__ . '/lib/LimeExtra/App.php');
    include(__DIR__ . '/lib/LimeExtra/Controller.php');
}

/*
 * Autoload from lib folder (PSR-0)
 */

spl_autoload_register(function ($class) {
    $class_path = __DIR__ . '/lib/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($class_path)) include_once($class_path);
});

// load .env file if exists
DotEnv::load(__DIR__);

// check for custom defines
if (file_exists(__DIR__ . '/defines.php')) {
    include(__DIR__ . '/defines.php');
}

/*
 * Collect needed paths
 */

$YXORP_DIR = str_replace(DIRECTORY_SEPARATOR, '/', __DIR__);
$YXORP_DOCS_ROOT = str_replace(DIRECTORY_SEPARATOR, '/', isset($_SERVER['DOCUMENT_ROOT']) ? realpath($_SERVER['DOCUMENT_ROOT']) : dirname(__DIR__));

# make sure that $_SERVER['DOCUMENT_ROOT'] is set correctly
if (strpos($YXORP_DIR, $YXORP_DOCS_ROOT) !== 0 && isset($_SERVER['SCRIPT_NAME'])) {
    $YXORP_DOCS_ROOT = str_replace(dirname(str_replace(DIRECTORY_SEPARATOR, '/', $_SERVER['SCRIPT_NAME'])), '', $YXORP_DIR);
}

$YXORP_BASE = trim(str_replace($YXORP_DOCS_ROOT, '', $YXORP_DIR), "/");
$YXORP_BASE_URL = strlen($YXORP_BASE) ? "/{$YXORP_BASE}" : $YXORP_BASE;
$YXORP_BASE_ROUTE = $YXORP_BASE_URL;

/*
 * SYSTEM DEFINES
 */
if (!defined('YXORP_DIR')) define('YXORP_DIR', $YXORP_DIR);
if (!defined('YXORP_ADMIN')) define('YXORP_ADMIN', 0);
if (!defined('YXORP_DOCS_ROOT')) define('YXORP_DOCS_ROOT', $YXORP_DOCS_ROOT);
if (!defined('YXORP_ENV_ROOT')) define('YXORP_ENV_ROOT', YXORP_DIR);
if (!defined('YXORP_BASE_URL')) define('YXORP_BASE_URL', $YXORP_BASE_URL);
if (!defined('YXORP_API_REQUEST')) define('YXORP_API_REQUEST', YXORP_ADMIN && strpos($_SERVER['REQUEST_URI'], YXORP_BASE_URL . '/api/') !== false ? 1 : 0);
if (!defined('YXORP_SITE_DIR')) define('YXORP_SITE_DIR', YXORP_ENV_ROOT == YXORP_DIR ? ($YXORP_DIR == YXORP_DOCS_ROOT ? YXORP_DIR : dirname(YXORP_DIR)) : YXORP_ENV_ROOT);
if (!defined('YXORP_CONFIG_DIR')) define('YXORP_CONFIG_DIR', YXORP_ENV_ROOT . '/config');
if (!defined('YXORP_BASE_ROUTE')) define('YXORP_BASE_ROUTE', $YXORP_BASE_ROUTE);
if (!defined('YXORP_STORAGE_FOLDER')) define('YXORP_STORAGE_FOLDER', YXORP_ENV_ROOT . '/storage');
if (!defined('YXORP_ADMIN_CP')) define('YXORP_ADMIN_CP', YXORP_ADMIN && !YXORP_API_REQUEST ? 1 : 0);
if (!defined('YXORP_PUBLIC_STORAGE_FOLDER')) define('YXORP_PUBLIC_STORAGE_FOLDER', YXORP_ENV_ROOT . '/storage');

if (!defined('YXORP_CONFIG_PATH')) {
    $_configpath = YXORP_CONFIG_DIR . '/config.' . (file_exists(YXORP_CONFIG_DIR . '/config.php') ? 'php' : 'yaml');
    define('YXORP_CONFIG_PATH', $_configpath);
}


function yxorp($module = null)
{

    static $app;

    if (!$app) {

        $customConfig = [];

        // load custom config
        if (file_exists(YXORP_CONFIG_PATH)) {
            $customConfig = preg_match('/\.yaml$/', YXORP_CONFIG_PATH) ? Spyc::YAMLLoad(YXORP_CONFIG_PATH) : include(YXORP_CONFIG_PATH);
        }

        // load config
        $config = array_replace_recursive([

            'debug' => preg_match('/(localhost|::1|\.local)$/', @$_SERVER['SERVER_NAME']),
            'app.name' => 'yxorP',
            'base_url' => YXORP_BASE_URL,
            'base_route' => YXORP_BASE_ROUTE,
            'docs_root' => YXORP_DOCS_ROOT,
            'session.name' => md5(YXORP_ENV_ROOT),
            'session.init' => (YXORP_ADMIN && !YXORP_API_REQUEST) ? true : false,
            'sec-key' => 'c3b40c4c-db44-s5h7-a814-b4931a15e5e1',
            'i18n' => 'en',
            'database' => ['server' => 'mongolite://' . (YXORP_STORAGE_FOLDER . '/data'), 'options' => ['db' => 'yxorpdb'], 'driverOptions' => []],
            'memory' => ['server' => 'redislite://' . (YXORP_STORAGE_FOLDER . '/data/yxorp.memory.sqlite'), 'options' => []],

            'paths' => [
                '#root' => YXORP_DIR,
                '#storage' => YXORP_STORAGE_FOLDER,
                '#pstorage' => YXORP_PUBLIC_STORAGE_FOLDER,
                '#data' => YXORP_STORAGE_FOLDER . '/data',
                '#cache' => YXORP_STORAGE_FOLDER . '/cache',
                '#tmp' => YXORP_STORAGE_FOLDER . '/tmp',
                '#thumbs' => YXORP_PUBLIC_STORAGE_FOLDER . '/thumbs',
                '#uploads' => YXORP_PUBLIC_STORAGE_FOLDER . '/uploads',
                '#modules' => YXORP_DIR . '/modules',
                '#addons' => YXORP_ENV_ROOT . '/addons',
                '#config' => YXORP_CONFIG_DIR,
                'assets' => YXORP_DIR . '/assets',
                'site' => YXORP_SITE_DIR
            ],

            'filestorage' => [],

        ], is_array($customConfig) ? $customConfig : []);

        // make sure yxorP module is not disabled
        if (isset($config['modules.disabled']) && in_array('yxorP', $config['modules.disabled'])) {
            array_splice($config['modules.disabled'], array_search('yxorP', $config['modules.disabled']), 1);
        }

        $app = new LimeExtra\App($config);

        $app['config'] = $config;

        // register paths
        foreach ($config['paths'] as $key => $path) {
            $app->path($key, $path);
        }

        // nosql storage
        $app->service('storage', function () use ($config) {
            $client = new MongoHybrid\Client($config['database']['server'], $config['database']['options'], $config['database']['driverOptions']);
            return $client;
        });

        // file storage
        $app->service('filestorage', function () use ($config, $app) {

            $storages = array_replace_recursive([

                'root' => [
                    'adapter' => 'League\Flysystem\Adapter\Local',
                    'args' => [$app->path('#root:')],
                    'mount' => true,
                    'url' => $app->pathToUrl('#root:', true)
                ],

                'site' => [
                    'adapter' => 'League\Flysystem\Adapter\Local',
                    'args' => [$app->path('site:')],
                    'mount' => true,
                    'url' => $app->pathToUrl('site:', true)
                ],

                'tmp' => [
                    'adapter' => 'League\Flysystem\Adapter\Local',
                    'args' => [$app->path('#tmp:')],
                    'mount' => true,
                    'url' => $app->pathToUrl('#tmp:', true)
                ],

                'thumbs' => [
                    'adapter' => 'League\Flysystem\Adapter\Local',
                    'args' => [$app->path('#thumbs:')],
                    'mount' => true,
                    'url' => $app->pathToUrl('#thumbs:', true)
                ],

                'uploads' => [
                    'adapter' => 'League\Flysystem\Adapter\Local',
                    'args' => [$app->path('#uploads:')],
                    'mount' => true,
                    'url' => $app->pathToUrl('#uploads:', true)
                ],

                'assets' => [
                    'adapter' => 'League\Flysystem\Adapter\Local',
                    'args' => [$app->path('#uploads:')],
                    'mount' => true,
                    'url' => $app->pathToUrl('#uploads:', true)
                ]

            ], $config['filestorage']);

            $app->trigger('yxorp.filestorages.init', [&$storages]);

            $filestorage = new FileStorage($storages);

            return $filestorage;
        });

        // key-value storage
        $app->service('memory', function () use ($config) {
            $client = new SimpleStorage\Client($config['memory']['server'], $config['memory']['options']);
            return $client;
        });

        // mailer service
        $app->service('mailer', function () use ($app, $config) {

            $options = isset($config['mailer']) ? $config['mailer'] : [];

            if (is_string($options)) {
                parse_str($options, $options);
            }

            $mailer = new \Mailer($options['transport'] ?? 'mail', $options);
            return $mailer;
        });

        // set cache path
        $tmppath = $app->path('#tmp:');

        $app('cache')->setCachePath($tmppath);
        $app->renderer->setCachePath($tmppath);

        // i18n
        $app('i18n')->locale = $config['i18n'] ?? 'en';

        // handle exceptions
        if (YXORP_ADMIN) {

            set_exception_handler(function ($exception) use ($app) {

                $error = [
                    'message' => $exception->getMessage(),
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                ];

                if ($app['debug']) {
                    $body = $app->request->is('ajax') || YXORP_API_REQUEST ? json_encode(['error' => $error['message'], 'file' => $error['file'], 'line' => $error['line']]) : $app->render('yxorp:views/errors/500-debug.php', ['error' => $error]);
                } else {
                    $body = $app->request->is('ajax') || YXORP_API_REQUEST ? '{"error": "500", "message": "system error"}' : $app->view('yxorp:views/errors/500.php');
                }

                $app->trigger('error', [$error, $exception]);

                header('HTTP/1.0 500 Internal Server Error');
                echo $body;

                if (function_exists('yxorp_error_handler')) {
                    yxorp_error_handler($error);
                }
            });
        }

        $modulesPaths = array_merge([
            YXORP_DIR . '/modules',  # core
            YXORP_DIR . '/addons' # addons
        ], $config['loadmodules'] ?? []);

        if (YXORP_ENV_ROOT !== YXORP_DIR) {
            $modulesPaths[] = YXORP_ENV_ROOT . '/addons';
        }

        // load modules
        $app->loadModules($modulesPaths);

        // load config global bootstrap file
        if ($custombootfile = $app->path('#config:bootstrap.php')) {
            include($custombootfile);
        }

        $app->trigger('yxorp.bootstrap');
    }

    // shorthand modules method call e.g. yxorp('regions:render', 'test');
    if (func_num_args() > 1) {

        $arguments = func_get_args();

        list($module, $method) = explode(':', $arguments[0]);
        array_splice($arguments, 0, 1);
        return call_user_func_array([$app->module($module), $method], $arguments);
    }

    return $module ? $app->module($module) : $app;
}

$yxorp = yxorp();