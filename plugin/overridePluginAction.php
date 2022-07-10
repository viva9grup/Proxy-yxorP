<?php
/* Importing the `wrapper` class from the `yxorP\inc\http` namespace. */

use yxorP\inc\constants;
use yxorP\inc\generalHelper;
use yxorP\inc\minify\minify;
use yxorP\inc\wrapper;

/* Importing the `generalHelper` class from the `yxorP\inc\http` namespace. */

/* Importing the `minify` class from the `yxorP\inc\minify` namespace. */

/* Extending the `wrapper` class. */

class overridePluginAction extends wrapper
{
    /* Overriding the `onEventWrite` method of the `wrapper` class. */
    public function onEventWrite(): void
    {
        /* Checking if the content type is not HTML, JavaScript, CSS, XML or text. If it is not, it will return. */
        if (constants::get('MIME') !== VAR_TEXT_HTML && constants::get('MIME') !== 'application/javascript' && constants::get('MIME') !== 'text/css' && constants::get('MIME') !== 'application/xml' && !str_contains(constants::get('MIME'), VAR_TEXT) && !str_contains(constants::get('MIME'), VAR_HTML)) return;
        /* It's setting the content of the response to the content of the `REWRITE` method. */
        $this->rewrite(str_replace(generalHelper::array_merge_ignore(array(YXORP_TARGET_DOMAIN), array_keys((array)constants::get(YXORP_GLOBAL_REPLACE)), array_keys((array)constants::get(VAR_TARGET_REPLACE))), generalHelper::array_merge_ignore(array(YXORP_SITE_DOMAIN), array_values((array)constants::get(YXORP_GLOBAL_REPLACE)), array_values((array)constants::get(VAR_TARGET_REPLACE))), preg_replace(generalHelper::array_merge_ignore(array_keys((array)constants::get(YXORP_GLOBAL_PATTERN)), array_keys((array)constants::get(VAR_TARGET_PATTERN))), generalHelper::array_merge_ignore(array_values((array)constants::get(YXORP_GLOBAL_PATTERN)), array_values((array)constants::get(VAR_TARGET_PATTERN))), constants::get(VAR_RESPONSE)->getContent())));
    }

    /* Minifying the content of the response. */
    private function rewrite($content): void
    {
        /* It's setting the `YXORP_REWRITE_SEARCH` constant to the value of the `PATH_REWRITE_SEARCH` constant. */
        constants::set(YXORP_REWRITE_SEARCH, generalHelper::CSV(PATH_REWRITE_SEARCH));
        /* It's setting the `YXORP_REWRITE_REPLACE` constant to the value of the `PATH_REWRITE_REPLACE` constant. */
        constants::set(YXORP_REWRITE_REPLACE, generalHelper::CSV(PATH_REWRITE_REPLACE));
        /* Minifying the content of the response. */

        /* Replacing the content of the response with the content of the `REWRITE` method. */
        constants::get(VAR_RESPONSE)->setContent((minify::createDefault())->process(constants::get('MIME') !== VAR_TEXT_HTML ? $content : preg_replace_callback("(<(p|span|div|li|ul)(.*)>(.*)</(p|span|div|li|ul)>)", static function ($m) {
            return str_replace(constants::get(YXORP_REWRITE_SEARCH), constants::get(YXORP_REWRITE_REPLACE), $m[3]);
        }, $content)));
    }

}