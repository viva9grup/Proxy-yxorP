<?php

/**
 * Importing the wrapper class from the yxorP\lib\http namespace.
 */

use yxorP\lib\http\helpers;
use yxorP\lib\http\wrapper;

/**
 * Extending the wrapper class.
 */
class onHeadersAction extends wrapper
{
    /**
     * Setting the headers for the request.
     *
     */
    public function onHeaders(): void
    {
        ini_set('display_startup_errors', ENV_DEBUG ? 1 : 0);
        ini_set('display_errors', ENV_DEBUG ? 1 : 0);
        error_reporting(ENV_DEBUG ? 1 : 0);
    }
}
