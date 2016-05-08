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
        if (!empty($url) && is_string($url)) {
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
        if (empty($path[0])) {
            array_shift($path);
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
        $path     = count($this[PATH]) ? '/'. implode( '/', $this[PATH]) : '';
        $query    = \PMVC\get($this[QUERY]);
        ksort($query);
        $query    = http_build_query($query);
        $query    = (($query && $path) ? '?': '') . $query; 
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
                'Key not exists. ['.$k.']',
                E_USER_WARNING
            );
        }
        return \PMVC\set($this->state, $k, $v);
    }
}
