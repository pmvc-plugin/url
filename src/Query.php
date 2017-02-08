<?php
namespace PMVC\PlugIn\url;

use PMVC\HashMap;

class Query extends HashMap
{
    public function __construct($query = null)
    {
        parent::__construct();
        $this->set($query);
    }

    public function set($query)
    {
        $array = $this->toArray($query);
        return \PMVC\set($this, $array);
    }

    public function toArray($query)
    {
        if (!\PMVC\isArray($query)) {
            $arr = self::parse_str($query);
            return $arr;
        } else {
            return $query;
        }
    }

    static public function parse_str($string)
    {
        parse_str($string,$arr);
        $new_arr = [];
        foreach($arr as $k=>$v){
            if ( false !== strpos($k,'_') &&
                    false === strpos($string,$k)
               ) {
                $new_k = str_replace('_','.',$k);
                if (false!==strpos($string,$new_k)) {
                    $new_arr[$new_k] = $v;
                    unset($arr[$k]);
                }
            }
        }
        $arr = array_replace($arr,$new_arr);
        return $arr;
    }

    public function stringify()
    {
        $array = \PMVC\get($this);
        ksort($array);
        $string = http_build_query($array); 
        return $string;
    }

    public function __tostring()
    {
        return $this->stringify();
    }
}
