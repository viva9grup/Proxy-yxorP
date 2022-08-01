<?php namespace yxorP\app\lib\proxy\Handler;

use yxorP\app\lib\Psr\Http\Message\RequestInterface;

interface curlFactoryInterface
{
    public function create(RequestInterface $request, array $options);

    public function release(easyHandle $easy);
}