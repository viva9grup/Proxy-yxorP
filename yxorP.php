<?php

use yxorP\cache\Cache;
use yxorP\cache\Check;
use yxorP\Helper\HeaderHelper;
use yxorP\Helper\IncludeHelper;


class yxorP
{
    public static $listeners = [];

    public function __construct()
    {
        $_plugins = $GLOBALS['SITE_CONTEXT']->SITE['plugins'] ?: [];
        array_push($_plugins, 'OverridePlugin');
        foreach ($_plugins as $plugin) {
            if (file_exists($GLOBALS['PLUGIN_DIR'] . '/plugin/' . $plugin . '.php')) {
                require($GLOBALS['PLUGIN_DIR'] . '/plugin/' . $plugin . '.php');
            } elseif (class_exists('\\yxorP\\plugin\\' . $plugin)) {
                $plugin = '\\yxorP\\plugin\\' . $plugin;
            }
            $this->addSubscriber(new $plugin());
        }
        HeaderHelper::helper();
    }

    public static function Proxy($REQUEST)
    {

        $GLOBALS['SERVER'] = $REQUEST;
        $GLOBALS['PLUGIN_DIR'] = __DIR__;
        require $GLOBALS['PLUGIN_DIR'] . '/cache/Cache.php';

        Check::fetch();
        error_reporting(!(int)str_contains($GLOBALS['SERVER']['SERVER_NAME'], '.'));
        if (Cache::cache($GLOBALS['CACHE_KEY'])->isValid()) return Cache::cache($GLOBALS['CACHE_KEY'])->get();
        error_reporting(1);

        if (str_contains($GLOBALS['SERVER']['REQUEST_URI'], '/cockpit')) {
            require $GLOBALS['PLUGIN_DIR'] . 'cockpit/index.php';
        }

        foreach (array('http', 'dom', 'helper', 'domain') as $_asset)
            self::FILES_CHECK($GLOBALS['PLUGIN_DIR'] . DIRECTORY_SEPARATOR . $_asset, true);

        new IncludeHelper();

        return Cache::cache($GLOBALS['CACHE_KEY'])->get();
    }

    public static function FILES_CHECK($dir, $inc)
    {
        if (file($dir) || is_dir($dir)) {
            foreach (scandir($dir) as $x) {
                if (strlen($x) > 3) {
                    if (str_contains($x, 'Interface')) {
                        continue;
                    }
                    if (is_dir($_loc = $dir . '/' . $x)) {
                        self::FILES_CHECK($_loc, $inc);
                    } else if ($inc) {
                        require_once($_loc);
                    } else if (str_contains($GLOBALS['SITE_CONTEXT']->REQUEST_URI, $x)) {
                        return Cache::cache($GLOBALS['CACHE_KEY'])->set(file_get_contents($_loc));
                    }
                }
            }
        }
    }

    public static function addListener($event, $callback, $priority = 0): void
    {
        self::$listeners[$event][$priority][] = $callback;
    }

    public static function CSV($filename = '')
    {
        $csvArray = array_map('str_getcsv', file($filename));
        return array_merge(...$csvArray);
    }

    public static function SUB($url)
    {
        $urlHostSegments = explode('.', parse_url($url)['host']);
        return (count($urlHostSegments) > 2) ? $urlHostSegments[0] : null;
    }

    public function addSubscriber($subscriber): void
    {
        if (method_exists($subscriber, 'subscribe')) {
            $subscriber->subscribe($this);
        }
    }
}
}