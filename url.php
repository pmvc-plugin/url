<?php
namespace PMVC\PlugIn\url;

${_INIT_CONFIG}[_CLASS] = __NAMESPACE__.'\url';
\PMVC\l(__DIR__.'/src/UrlObject.php');

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
      function getUrl($url)
      {
          return new UrlObject($url);
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
        $uri = str_replace('#','%23',$uri);
        $s='/';
        if ( !$this['SCRIPT_NAME'] || 
             ( false === strpos($this['SCRIPT_NAME'], $uri) && 
                false === strpos($uri, $this['SCRIPT_NAME'])
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
        $url = $this['REQUEST_URI'];
        $end = strrpos($url, $path);
        $url = substr($url, 0, $end);
        return $this->toHttp($url);
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


    public function tohttp($url, $type=null)
    {
        if (!preg_match('/^(\/\/|http)/i', $url)) {
            if (is_null($type)) {
                $type= $this->getProtocol();
            }
            $url = $this->getUrl($url);
            $url->scheme = $type;
            $url->host = $this['HTTP_HOST'];
        }
        return (string)$url;
    }

    public function initEnv()
    {
        $this->setEnv([
            'HTTPS',
            'HTTP_HOST',
            'HTTP_X_FORWARDED_PROTO',
            'SCRIPT_NAME',
            'REQUEST_URI'
        ], false);
        if ('https' === $this['HTTP_X_FORWARDED_PROTO']) {
            $this['HTTPS'] = 'on';
        }
    }

    public function init()
    {
        $this->initEnv();
    }
}
