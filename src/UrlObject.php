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
        parent::__construct(null);
        $this->set($url);
    }

    /**
     * Get Initial State.
     *
     * @return array
     */
    protected function getInitialState()
    {
        return [
             SCHEME   =>'',
             HOST     =>'',
             PORT     =>'',
             USER     =>'',
             PASS     =>'',
             PATH     =>[],
             QUERY    =>new \PMVC\HashMap(),
             FRAGMENT =>''
        ];
    }

    function set($url)
    {
        if (!empty($url) && !is_array($url)) {
            $url = parse_url($url);
        }
        if (!empty($url[PATH])) {
            if(!empty($url[HOST])){
                $this->prependPath($url[PATH]);
            }else{
                $this->appendPath($url[PATH]);
            }
            unset($url[PATH]);
        }
        \PMVC\set($this,$url);
        return $this;
    }

    function queryToArray($query)
    {
        if (!is_array($query)) {
            parse_str($query,$query);
        }
        return $query;
    }

    function pathToArray($path)
    {
        if (is_array($path)) {
            return $path;
        }
        $path = explode('/',$path);
        if (empty(end($path))) {
            array_pop($path);
        }
        return $path;
    }

    function appendPath($path)
    {
        $path = $this->pathToArray($path);
        $this[PATH] = array_merge($this[PATH],$path);
    }

    function prependPath($path)
    {
        $path = $this->pathToArray($path);
        if (empty($this[PATH][0])) {
            array_shift($this[PATH]);
        }
        $this[PATH] = array_merge($path,$this[PATH]);
    }

    function stringify()
    {
        $scheme   = !empty($this[SCHEME]) ? $this[SCHEME].'://' : ''; 
        $host     = $this[HOST]; 
        $port     = !empty($this[PORT]) ? ':' . $this[PORT] : ''; 
        $user     = $this[USER]; 
        $pass     = !empty($this[PASS]) ? ':' . $this[PASS]  : ''; 
        $pass     = ($user || $pass) ? $pass.'@' : ''; 
        $query    = \PMVC\get($this[QUERY]);
        ksort($query);
        $query    = http_build_query($query);
        $query    = (($query) ? '?': '') . $query; 
        $path     = implode( '/', $this[PATH]);
        $path     = (( 
            $host && 
            0!==strpos($path,'/') &&  
            ($path || $query)
        ) ? '/' : '').$path;
        $fragment = !empty($this[FRAGMENT])?'#'. $this[FRAGMENT] : ''; 
        return $scheme.$user.$pass.$host.$port.$path.$query.$fragment; 
    }

    public function path($path)
    {
        $path = $this->pathToArray($path);
        return parent::offsetSet(PATH, $path);
    }

    public function query($query)
    {
        $query = $this->queryToArray($query);
        return \PMVC\set($this[QUERY],$query);
    }

    public function __tostring()
    {
        return $this->stringify();
    }

    public function __get($k)
    {
        return $this[$k];
    }

    public function offsetSet($k, $v)
    {
        if (is_callable([$this,$k])) {
            return $this->$k($v);
        }
        if (!isset($this[$k])) {
            return !trigger_error(
                '[PMVC\PlugIn\url\UrlObject] Key not exists. ('.$k.')',
                E_USER_WARNING
            );
        }
        return \PMVC\set($this->state, $k, $v);
    }
}
