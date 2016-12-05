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
     private $protocol=null;

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
        $uri = $this['REQUEST_URI'];
        if ( !$this['SCRIPT_NAME'] ||
            false===strpos($uri, $this['SCRIPT_NAME']) 
           ) {
            // http://xxx/path use rewrite rule
            return $uri;
        } else {
            $run = $this->getRunPhp();
            $start = strpos($uri, $run)+ strlen($run);
        }
        $s = substr($uri, $start, strlen($uri));
        if (0===strpos($s, '?')) {
            $s = substr($s, 1); 
        }
        if (false!==strpos($s, '?')) {
            $s = substr($s, 0, strpos($s, '?'));
        }
        if (!$s) {
            $s='/';
        }
        return $s;
    }

    public function realUrl()
    {
        $url = $this['REQUEST_URI'];
        return $this->toHttp($url);
    }

   /**
    * get http or https
    */
    public function getProtocol()
    {
        if (!is_null($this->protocol)) {
            return $this->protocol;
        } else {
            $this->protocol = ('on'!=$this['HTTPS']) ? 'http' : 'https';
            return $this->protocol;
        }
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
            'SCRIPT_NAME',
            'REQUEST_URI'
        ], false);
    }

    public function init()
    {
        $this->initEnv();
    }
}
