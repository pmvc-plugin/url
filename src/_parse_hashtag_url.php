<?php

namespace PMVC\PlugIn\url;

${_INIT_CONFIG}[_CLASS] = __NAMESPACE__.'\ParseHashtagUrl';

/**
 * @see https://bugs.php.net/bug.php?id=73192
 */
class ParseHashtagUrl
{
    function __invoke($url)
    {
        $hashPos = strpos($url, '#');
        $dbSlashPos = strpos($url, '//');
        $slashPos = strpos($url, '/', $dbSlashPos+2);
        if ($slashPos > $hashPos) {
            $url = substr($url,0,$hashPos).
                '/#'.
                substr($url,$hashPos+1);
        }
        return parse_url($url);
    }
}
