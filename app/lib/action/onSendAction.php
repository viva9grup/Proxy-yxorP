<?php
/* Importing the wrapper class from the yxorP\app\lib\http namespace. */


use yxorP\app\constants;
use yxorP\app\lib\http\wrapper;
use yxorP\app\yP;

/* Extending the `wrapper` class, which is a class that is used to wrap the `event` class. */

class onSendAction extends wrapper
{
    /* A method that is called before the request is sent. */
    /**
     * @throws JsonException
     */
    public function onSend(): void
    {
        /* Creating a new `yxorP\app\lib\proxy\Client` object, and then it is sending a request to the `\yxorP\app\yP::get(YXORP_FETCH)` URL,
        with the `\yxorP\app\yP::get(YXORP_REQUEST)->getMethod()` method, and the `$_REQUEST` array as the body.   Setting the content of the response to the content of the request. */
        yP::get(VAR_RESPONSE)->setContent((yP::get(VAR_GUZZLE)->request(yP::get(VAR_REQUEST)->getMethod(), yP::get(VAR_REQUEST)->getUrl(), json_decode(json_encode($_REQUEST), true, 512, JSON_THROW_ON_ERROR)))->getBody());
    }
}