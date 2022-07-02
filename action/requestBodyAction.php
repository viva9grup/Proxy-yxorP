<?php
/* Importing the actionWrapper class from the yxorP\http namespace. */

use yxorP\inc\actionWrapper;
use yxorP\inc\constants;

/* Extending the actionWrapper class. */

class requestBodyAction extends actionWrapper
{
    /* A method that is called by the actionWrapper class. */
    public function buildRequest(): void
    {
        /* Getting the request body from the input stream and setting it to the request object. */
        if ($_body = file_get_contents('php:' . CHAR_SLASH . CHAR_SLASH . 'input')) constants::get(YXORP_REQUEST)->setBody(json_decode($_body, true), constants::get('MIME'));
    }
}