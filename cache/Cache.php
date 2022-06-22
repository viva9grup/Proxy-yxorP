<?php namespace yxorP\cache;
require $GLOBALS['PLUGIN_DIR'] . '/cache/State.php';
require $GLOBALS['PLUGIN_DIR'] . '/cache/Check.php';

class Cache
{
    public const EXT = '.tmp';
    public const OPTIONS = '.attr';
    public static $is_pretty = false;
    private static $instance;
    private $attr_instance;
    private $path;
    private $key;
    private $options;

    private function __construct($key, $is_super = true)
    {
        $this->path = $GLOBALS['CACHE_DIR'];
        $this->key = $key;
        $this->options = ['expiry' => -1, 'lock' => false,];
        if ($is_super) {
            $this->attr_instance = new self($this->key . Cache::OPTIONS, false);
            if ($this->attr_instance->isExists()) {
                $this->options = $this->attr_instance->get();
            }
        }
    }

    private function isExists()
    {
        if (file_exists($this->path . "$this->key")) {
            return true;
        } else {
            return false;
        }
    }

    public function get()
    {
        if (!$this->isValid()) {
            return;
        }
        @include $this->path . "$this->key";
        return $val ?? false;
    }

    public function isValid()
    {
        if ($this->options['expiry'] !== -1 && $this->options['expiry'] < time()) {
            return false;
        }
        if (!$this->isExists()) {
            return false;
        }
        return true;
    }

    public static function cache($key)
    {
        if (!isset(self::$instance[$key])) {
            self::$instance[$key] = new self($key);
        }
        return self::$instance[$key];
    }

    public static function clearAll()
    {
        $files = glob($GLOBALS['CACHE_DIR'] . '*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }

    public function lock()
    {
        $this->options['lock'] = true;
        $this->attrSave();
        return $this;
    }

    private function attrSave()
    {
        $this->attr_instance->set($this->options);
    }

    public function set($val)
    {
        $key = $this->key;
        if ($this->options['lock']) {
            return $this;
        }
        $val = var_export($val, true);
        if (!self::$is_pretty) {
            $val = str_replace(["\\n", ",  '", " => "], ["", ",'", "=>"], $val);
        }
        $val = str_replace('stdClass::__set_state', '(object)', $val);
        $tmp = $this->path . "$key." . uniqid('', true) . Cache::EXT;
        $file = fopen($tmp, 'x');
        fwrite($file, '<?php $val=' . $val . ';');
        fclose($file);
        rename($tmp, $this->path . $key);
        return $this;
    }


    public function options($options)
    {
        $this->options = array_merge($this->options, $options);
        $this->attrSave();
        return $this;
    }

}
