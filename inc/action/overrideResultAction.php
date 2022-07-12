<?php
/* Importing the `wrapper` class from the `yxorP\inc\http` namespace. */

use yxorP\inc\constants;
use yxorP\inc\generalHelper;
use yxorP\inc\minify\minify;
use yxorP\inc\wrapper;

/* Importing the `generalHelper` class from the `yxorP\inc\http` namespace. */

/* Importing the `minify` class from the `yxorP\inc\minify` namespace. */

/* Extending the `wrapper` class. */

class overrideResultAction extends wrapper
{
    /* Overriding the `onEventWrite` method of the `wrapper` class. */
    private static function result($m)
    {
        print_r($m);
        return (is_array($m) && count($m)) ? str_replace('This', 'That', $m[0]) : null;
    }

    private static function replacer($text)
    {
        $text = htmlspecialchars($text);
        $text = preg_replace("/=/", "=\"\"", $text);
        $text = preg_replace("/" / ", ""\"", $text)
        $tags = "/<(\/|)(\w*)(\ |)(\w*)([\\\=]*)(?|(\")\""\"|)(?|(.*)?"(\")|)([\ ]?)(\/|)>/i";
        $replacement = "<$1$2$3$4$5$6$7$8$9$10>";
        $text = preg_replace($tags, $replacement, $text);
        $text = preg_replace("/=\"\"/", "=", $text);
        return $text;
        }

    public function onEventWrite(): void
    {
        /* Checking if the content type is not HTML, JavaScript, CSS, XML or text. If it is not, it will return. */
        if (MIME === VAR_TEXT_HTML || MIME === 'application/javascript' || MIME === 'text/css' || MIME === 'application/xml' || str_contains(MIME, VAR_TEXT) || str_contains(MIME, VAR_HTML)) self::replace(constants::get(VAR_RESPONSE)->setContent(str_replace(generalHelper::array_merge_ignore(array(YXORP_TARGET_DOMAIN), array_keys((array)constants::get(YXORP_GLOBAL_REPLACE)), array_keys((array)constants::get(VAR_TARGET_REPLACE))), generalHelper::array_merge_ignore(array(YXORP_SITE_DOMAIN), array_values((array)constants::get(YXORP_GLOBAL_REPLACE)), array_values((array)constants::get(VAR_TARGET_REPLACE))), preg_replace(generalHelper::array_merge_ignore(array_keys((array)constants::get(YXORP_GLOBAL_PATTERN)), array_keys((array)constants::get(VAR_TARGET_PATTERN))), generalHelper::array_merge_ignore(array_values((array)constants::get(YXORP_GLOBAL_PATTERN)), array_values((array)constants::get(VAR_TARGET_PATTERN))), constants::get(VAR_RESPONSE)->getContent()))));
    }

    private static function replace($content)
    {
        /* Minifying the content of the response. Replacing the content of the response with the content of the `REWRITE` method. */
        if ($content) {
            echo MIME;
            echo VAR_TEXT_HTML;
            if (MIME !== VAR_TEXT_HTML) echo "true";
            constants::get(VAR_RESPONSE)->setContent((minify::createDefault())->process(self::callback($content)));

            print_r(constants::get(VAR_RESPONSE)->getContent());
            exit;
        }

    }

    private static function callback($content): string
    {
        $fileContent = preg_replace_callback('/^(.*x.*)(.*x.*)', function ($matches) {
            $matches[0] = str_replace('controls', 'controls controlsList=&quot;nodownload&quot;', $match);
            return $matches[0];
        }, $fileContent);
        if (MIME !== VAR_TEXT_HTML) {
            return $content;
        } else {
            return preg_replace_callback("<x(.*)>(.*)</x>", "self::result", $content);
        }
    }

}