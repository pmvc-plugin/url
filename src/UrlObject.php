<?php

namespace PMVC\PlugIn\url;

use InvalidArgumentException;

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
             PORT     =>null,
             USER     =>'',
             PASS     =>'',
             PATH     =>[],
             QUERY    =>new Query(),
             FRAGMENT =>''
        ];
    }

    function set($url)
    {
        if (!empty($url) && !is_array($url)) {
            if (false !== strpos($url, '#')) {
                $url = \PMVC\plug('url')->parse_hashtag_url($url);
            } else {
                $url = parse_url($url);
            }
        }
        if (!empty($url[PATH])) {
            if(!empty($url[HOST])){
                $this->prependPath($url[PATH]);
            }else{
                $this->appendPath($url[PATH]);
            }
            unset($url[PATH]);
        }
        if ($url) {
            \PMVC\set($this,$url);
        }
        return $this;
    }

    function pathToArray($path)
    {
        if (is_array($path)) {
            return $path;
        }
        $path = explode('/',$path);
        return $path;
    }

    function appendPath($path)
    {
        $path = $this->pathToArray($path);
        if (empty(end($this[PATH]))) {
            array_pop($this[PATH]);
        }
        if (empty($path[0]) && !empty($this[PATH]) && 1 < count($path)) {
            array_shift($path);
        }
        $this[PATH] = array_merge($this[PATH],$path);
    }

    function prependPath($path)
    {
        $path = $this->pathToArray($path);
        if (empty($this[PATH][0])) {
            array_shift($this[PATH]);
        }
        if ( empty(end($path)) && !empty($this[PATH]) ) {
            array_pop($path);
        }
        $this[PATH] = array_merge($path,$this[PATH]);
    }

    function stringify()
    {
        if (!empty($this[SCHEME])) {
            $scheme = $this[SCHEME].'://';
        } elseif (false===$this[SCHEME]) {
            $scheme = '//';
        } else {
            $scheme = '';
        }
        $host     = $this[HOST]; 
        $port     = !empty($this[PORT]) ? ':' . $this[PORT] : ''; 
        $user     = $this[USER]; 
        $pass     = !empty($this[PASS]) ? ':' . $this[PASS]  : ''; 
        $pass     = ($user || $pass) ? $pass.'@' : ''; 
        $query    = (string)$this[QUERY];
        $query    = (($query) ? '?': '') . $query; 
        $path     = $this->getPath();
        $path     = (( 
            $host && 
            0!==strpos($path,'/') &&  
            ($path || $query)
        ) ? '/' : '').$path;
        $fragment = !empty($this[FRAGMENT])?'#'. $this[FRAGMENT] : ''; 
        return $scheme.$user.$pass.$host.$port.$path.$query.$fragment; 
    }

    public function getPath()
    {
        return implode( '/', $this[PATH]);
    }

    public function path($path)
    {
        $path = $this->pathToArray($path);
        return parent::offsetSet(PATH, $path);
    }

    public function query($query)
    {
        return $this[QUERY]->set($query);
    }

    public function port($num)
    {
        if (is_null($num)) {
            return;
        }
        if (!is_numeric($num)) {
            throw new InvalidArgumentException('Port is not number. ['.$num.']');
        }
        return parent::offsetSet(PORT, (int)$num);;
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
            throw new InvalidArgumentException('Invalid key. ['.$k.']');
        }
        return parent::offsetSet($k, $v);
    }
}
