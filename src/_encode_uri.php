<?php

namespace PMVC\PlugIn\url;

${_INIT_CONFIG}[_CLASS] = __NAMESPACE__.'\EncodeUri';

/**
 *  Encode URI
 */
class EncodeUri
{
    private $_from;
    private $_to;
    public function __construct()
    {
        $map = [
            '%23'=>'＃',
            '%26'=>'&',
            '%5B'=>'[',
            '%5D'=>']',
            '%7B'=>'',
            '%7D'=>'',
        ];
        $this->_from = array_keys($map);
        $this->_to = array_values($map);
    }

    public function __invoke()
    {
        return $this;
    }

    public function encode($uri)
    {
        $uri = urlencode($uri);
        $uri = str_replace(
            $this->_from,
            $this->_to,
            $uri
        );
        return $uri;
    }
}
