<?php
namespace PMVC\PlugIn\url;

use PMVC\Event;

${_INIT_CONFIG}[_CLASS] = __NAMESPACE__.'\url';

\PMVC\l(__DIR__.'/src/UrlObject.php');
\PMVC\l(__DIR__.'/src/Query.php');

\PMVC\initPlugIn(['getenv'=>null]);

class url extends \PMVC\PlugIn
{
    /**
     * Keep value to check now use http or https
     * @var string
     */
     private $_protocol=null;

     /**
      * Cache getpath.
      * @var string
      */
      private $_path;

    /**
     * Set env
     */
     public function setEnv(array $arr, $overwrite = true)
     {
         $env = \PMVC\plug('getenv');
         foreach ($arr as $key) {
             if ( $overwrite || !isset($this[$key]) ) {
                $this[$key] = $env->get($key);
             }
         }
     }
    
    /**
     * Get Url
     */
    public function getUrl($url)
    {
        if (!is_object($url)) {
            return new UrlObject($url);
        } else {
            if (!is_a($url, __NAMESPACE__.'\UrlObject')) {
                trigger_error('It is not url object.'.print_r($url, true));
            }
            return $url;
        }
    }

    /**
     * Get Url
     */
    public function getQuery($query)
    {
        return new Query($query);
    }

     /**
      * Set Url
      */
     public function seturl($url,$key,$value){
         $reg = '/([#?&]'.$key.'=)[^&#]*/';
         preg_match($reg,$url,$match);
         if(!empty($match)){
             $url = preg_replace($reg,'${1}'.$value,$url);
         }else{
             $url.=(false === strpos($url,'?')) ? '?' : '&'; 
             $url.=$key.'='.urlencode($value);
         }   
         return $url;
     } 
    
    public function getRunPhp()
    {
        return basename($this['SCRIPT_NAME']);
    }

    /**
     * Get path from environment.
     * @access public
     * @return string
     */
    public function getPath()
    {
        if (!empty($this->_path)) {
            return $this->_path;
        }
        $uri = $this['REQUEST_URI'];
        $s='/';
        if (empty($uri)) {
            $this->_path = $s;
            return $s;
        }
        $scriptFolder = dirname($this['SCRIPT_NAME']).'/?';
        if ( empty($this['SCRIPT_NAME']) || 
             ( false === strpos($this['SCRIPT_NAME'], $uri) && 
               false === strpos($uri, $this['SCRIPT_NAME']) &&
               false === strpos($uri, $scriptFolder) // http://xxx/?/path
             )
           ) {
            // http://xxx/path use rewrite rule
            $s = $this->getUrl($uri)->getPath();
        } elseif (false !== strpos($uri, $this['SCRIPT_NAME'])) {
            // http://xxx/index.php/path
            $run = $this->getRunPhp();
            $start = strpos($uri, $run)+ strlen($run);
            $s = substr($uri, $start);
        }
        if (empty($s)) {
            $s = '/';
        }
        if (0===strpos($s, '?')) {
            $s = substr($s, 1); 
        }
        if (false!==strpos($s, '?')) {
            $s = substr($s, 0, strpos($s, '?'));
        }
        $this->_path = $s;
        return $s;
    }

    public function realUrl()
    {
        $path = $this->getPath();
        // clean querystring
        $url = explode('?', $this['REQUEST_URI'])[0];
        if ($path && !('/'===$path && '/'!==substr($url, -1))) {
            $end = strrpos($url, $path);
            $url = substr($url, 0, $end);
        }
        return $this->toHttp($url);
    }

    public function pathToUrl($path)
    {
        $url = $this->getUrl($path);
        if (empty($url->scheme) && empty($url->host)) {
            return \PMVC\getOption('realUrl').$path;
        } else {
            return $this->toHttp($url);
        }
    } 

   /**
    * get http or https
    */
    public function getProtocol()
    {
        if (empty($this->_protocol)) {
            $this->_protocol = ('on'!=$this['HTTPS']) ? 'http' : 'https';
        }
        return $this->_protocol;
    }

    public function toHttp($url, $scheme=null)
    {
        $url = $this->getUrl($url);
        if (empty($url->scheme)) {
            if (is_null($scheme)) {
                $scheme = $this->getProtocol();
            }
            $url->scheme = $scheme;
        }
        if (empty($url->host)) {
            $url->host = $this[HOST];
        }
        return (string)$url;
    }

    public function initEnv()
    {
        $this->setEnv([
            'HTTPS',
            'HTTP_HOST',
            'HTTP_X_FORWARDED_PROTO',
            'HTTP_X_FORWARDED_HOST',
            'SCRIPT_NAME',
            'REQUEST_URI'
        ], false);
        if ('https' === $this['HTTP_X_FORWARDED_PROTO']) {
            $this['HTTPS'] = 'on';
        }
        if (!empty($this['HTTP_X_FORWARDED_HOST'])) {
            //https://httpd.apache.org/docs/current/mod/mod_proxy.html#x-headers
            $host = explode(',', $this['HTTP_X_FORWARDED_HOST']);
            $this['HTTP_HOST'] = $host[0];
        }
        $this[HOST] = $this->getDefaultHost();
        $this['REQUEST_URI'] = str_replace('#','%23',$this['REQUEST_URI']);
    }

    public function getDefaultHost()
    {
        return $this->getUrl($this['HTTP_HOST'])[HOST];
    }

    public function init()
    {
        \PMVC\callPlugin(
            'dispatcher',
            'attachAfter',
            [ 
                $this,
                Event\MAP_REQUEST,
            ]
        );
        $this->initEnv();
    }

    public function onMapRequest($subject)
    {
        $subject->detach($this);
        $host = \PMVC\getOption(HOST);
        if ($host) {
            $this[HOST] = $host;
        }
        if (\PMVC\exists('http', 'plugin')) {
            //value effect by $this[HOST]
            \PMVC\plug('controller')['realUrl'] = \PMVC\plug('url')->realUrl();
        }
    }
}
