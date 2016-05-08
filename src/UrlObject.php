<?php
namespace PMVC\PlugIn\url;

const SCHEME = 'scheme';
const HOST = 'host';
const PORT = 'port';
const USER = 'user';
const PASS = 'pass';
const PATH = 'path';
const QUERY = 'query';
const FRAGMENT = 'fragment';

/**
 * @doc https://en.wikipedia.org/wiki/Fragment_identifier
 */
class UrlObject extends \PMVC\HashMap
{
    function __construct($url=null)
    {
        if (!empty($url) && is_string($url)) {
            $url = $this->parse($url);
        }
        parent::__construct($url);
    }

    /**
     * Get Initial State.
     *
     * @return array
     */
    protected function getInitialState()
    {
        return [
             SCHEME   =>null,
             HOST     =>null,
             PORT     =>null,
             USER     =>null,
             PASS     =>null,
             PATH     =>null,
             QUERY    =>null,
             FRAGMENT =>null
        ];
    }

    function parse($url)
    {
        $arr = parse_url($url);
        $arr[PATH] = explode('/',$arr[PATH]);
        array_shift($arr[PATH]);
        parse_str($arr[QUERY],$arr[QUERY]);
        return $arr;
    }

    function stringify()
    {
        $scheme   = isset($this[SCHEME]) ? $this[SCHEME].'://' : ''; 
        $host     = $this[HOST]; 
        $port     = isset($this[PORT]) ? ':' . $this[PORT] : ''; 
        $user     = $this[USER]; 
        $pass     = isset($this[PASS]) ? ':' . $this[PASS]  : ''; 
        $pass     = ($user || $pass) ? $pass.'@' : ''; 
        $path     = count($this[PATH]) ? '/'.implode('/',$this[PATH]) : '';
        ksort($this[QUERY]);
        $query    = http_build_query($this[QUERY]);
        $query    = $query ? '?' . $query : ''; 
        $fragment = isset($this[FRAGMENT])?'#'. $this[FRAGMENT] : ''; 
        return $scheme.$user.$pass.$host.$port.$path.$query.$fragment; 
    }

    function __tostring()
    {
        return $this->stringify();
    }
}
