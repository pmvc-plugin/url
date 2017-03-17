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
    
    /**
     * More options
     * http://php.net/manual/en/function.call-user-func-array.php
     */
    public function stringify()
    {
        $params = func_get_args();
        $array = \PMVC\get($this);
        ksort($array);
        array_unshift($params, $array);
        return call_user_func_array(
            'http_build_query',
            $params
        );
    }

    public function __get($k)
    {
        return $this[$k];
    }

    public function __tostring()
    {
        return $this->stringify();
    }
}
