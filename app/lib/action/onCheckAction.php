<?php use yxorP\app\lib\http\cache;
use yxorP\app\lib\http\wrapper;

class onCheckAction extends wrapper
{
    public function onCheck(): self
    {
        if (cache::isValid()) cache::get();
        return $this;
    }
}
