<?php namespace yxorP\app\lib\proxy\Psr7;

use RuntimeException;
use yxorP\app\lib\Psr\Http\Message\StreamInterface;

class NoSeekStream implements StreamInterface
{
    use AStreamDecoratorTrait;

    public function seek($offset, $whence = SEEK_SET)
    {
        throw new RuntimeException('Cannot seek a NoSeekStream');
    }

    public function isSeekable()
    {
        return false;
    }
}