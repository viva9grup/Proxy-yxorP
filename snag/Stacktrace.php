<?php namespace yxorP\snag;

use InvalidArgumentException;
use RuntimeException;
use SplFileObject;

class Stacktrace
{
    const NUM_LINES = 7;
    const MAX_LENGTH = 200;
    protected $config;
    protected $frames = [];

    public function __construct(Configuration $config)
    {
        $this->config = $config;
    }

    public static function generate(Configuration $config)
    {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS & ~DEBUG_BACKTRACE_PROVIDE_OBJECT);
        return static::fromBacktrace($config, $backtrace, '[generator]', 0);
    }

    public static function fromBacktrace(Configuration $config, array $backtrace, $topFile, $topLine)
    {
        $stacktrace = new static($config);
        foreach ($backtrace as $frame) {
            if (!static::frameInsideSnag($frame)) {
                $stacktrace->addFrame($topFile, $topLine, isset($frame['function']) ? $frame['function'] : null, isset($frame['class']) ? $frame['class'] : null);
            }
            if (isset($frame['file']) && isset($frame['line'])) {
                $topFile = $frame['file'];
                $topLine = $frame['line'];
            } else {
                $topFile = '[internal]';
                $topLine = 0;
            }
        }
        $stacktrace->addFrame($topFile, $topLine, '[main]');
        return $stacktrace;
    }

    public static function frameInsideSnag(array $frame)
    {
        return isset($frame['class']) && strpos($frame['class'], 'yxorP\snag\\') === 0 && substr_count($frame['class'], '\\') === 1;
    }

    public function addFrame($file, $line, $method, $class = null)
    {
        $matches = [];
        if (preg_match("/^(.*?)\((\d+)\) : (?:eval\(\)'d code|runtime-created function)$/", $file, $matches)) {
            $file = $matches[1];
            $line = $matches[2];
        }
        $frame = ['lineNumber' => (int)$line, 'method' => $class ? "$class::$method" : $method,];
        if ($this->config->shouldSendCode()) {
            $frame['code'] = $this->getCode($file, $line, static::NUM_LINES);
        }
        $frame['inProject'] = $this->config->isInProject($file);
        $frame['file'] = $this->config->getStrippedFilePath($file);
        $this->frames[] = $frame;
    }

    protected function getCode($path, $line, $numLines)
    {
        if (empty($path) || empty($line) || !file_exists($path)) {
            return null;
        }
        try {
            $file = new SplFileObject($path);
            $file->seek(PHP_INT_MAX);
            $bounds = static::getBounds($line, $numLines, $file->key() + 1);
            $code = [];
            $file->seek($bounds[0] - 1);
            while ($file->key() < $bounds[1]) {
                $code[$file->key() + 1] = rtrim(substr($file->current(), 0, static::MAX_LENGTH));
                $file->next();
            }
            return $code;
        } catch (RuntimeException $ex) {
            return null;
        }
    }

    protected static function getBounds($line, $num, $max)
    {
        $start = max($line - floor($num / 2), 1);
        $end = $start + ($num - 1);
        if ($end > $max) {
            $end = $max;
            $start = max($end - ($num - 1), 1);
        }
        return [$start, $end];
    }

    public static function fromFrame(Configuration $config, $file, $line)
    {
        $stacktrace = new static($config);
        $stacktrace->addFrame($file, $line, '[unknown]');
        return $stacktrace;
    }

    public function &toArray()
    {
        return $this->frames;
    }

    public function &getFrames()
    {
        return $this->frames;
    }

    public function removeFrame($index)
    {
        if (!isset($this->frames[$index])) {
            throw new InvalidArgumentException('Invalid frame index to remove.');
        }
        array_splice($this->frames, $index, 1);
    }
}