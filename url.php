<?php
namespace PMVC\PlugIn\url;

${_INIT_CONFIG}[_CLASS] = __NAMESPACE__.'\url';

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
     public function setEnv($arr)
     {
         foreach (\PMVC\toArray($arr) as $key) {
             $this[$key] = getenv($key);
         }
     }
    
    /**
     * $att['url']
     * $att['action']
     * $att['del']
     * $att['separator']
     */
    public function getUrl($att=array())
    {
        $myUrl = $this->actionToUrl($att['action'], $att['url']);
        $separator = $this->getSeparator($att['separator']);
        $urls = \PMVC\get($this); //don't want effect $this->url
        if (is_array($att['del'])) {
            foreach ($att['del'] as $k) {
                unset($urls[$k]);
            }
        }
        if (PMVC\n($urls)) {
            return $myUrl.$seprator.$this->arrayToUrl($urls, null, $seprator);
        } else {
            return $myUrl;
        }
    }


    public function getRunPhp()
    {
        return basename($this['SCRIPT_NAME']);
    }


    /**
     * Get path information from the environment.
     * @access public
     * @return string
     */
    public function getPathInfo()
    {
        $uri = $this['REQUEST_URI'];
        if (false===strpos($uri, $this['SCRIPT_NAME'])) {
            $start = strpos($uri, '?')+1;
        } else {
            $start = strpos($uri, $this->getRunPhp())+strlen($this->getRunPhp());
        }
        $s = substr($uri, $start, strlen($uri));
        if (false!==strpos($s, '?')) {
            $s = substr($s, 0, strpos($s, '?'));
        }
        if (!$s) {
            $s='/';
        }
        return $s;
    }


    public function actionToUrl($action=null, $url=null)
    {
        $routing = \PMVC\getOption(_ROUTING);
        if ($routing) {
            return \PMVC\plug($routing)->actionToUrl($action, $url);
        }
    }

    public function realUrl()
    {
        $args =& func_get_args();
        $url = call_user_func_array(array($this,'actionToUrl'), $args);
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
        if (!preg_match('/^http/i', $url)) {
            if (is_null($type)) {
                $type= $this->getProtocol();
            }
            $url = $type.'://'.$this['HTTP_HOST'].$url;
        }
        return $url;
    }

    public function arrayToUrl($arr, $parent=null, $seprator=null, $isEncode=true)
    {
        if (!is_array($arr)) {
            return null;
        }
        $seprator = $this->getSeparator($seprator);
        foreach ($arr as $k=>$v) {
            $newParent = $parent;
            $newParent[] = $k;
            if (is_array($v)) {
                $return.=(($return)?$seprator:'').$this->arrayToUrl($v, $newParent, $seprator, $isEncode);
                continue;
            }
            if (empty($parent)) {
                $newKey = $this->getEncode($k, $isEncode);
            } else {
                $newKey = $this->getEncode($newParent[0], $isEncode);
                unset($newParent[0]);
                foreach ($newParent as $v1) {
                    $newKey .='['.$this->getEncode($v1, $isEncode).']';
                }
            }
            $return.=(($return)?$seprator:'').$newKey.'='.$this->getEncode($v, $isEncode);
        }
        return $return;
    }

    public function getEncode($string, $isEncode)
    {
        if ($isEncode) {
            return urlencode($string);
        } else {
            return $string;
        }
    }

    public function getSeparator($seprator=null)
    {
        return (is_null($seprator))?_URL_SPLIT:$seprator;
    }

    public function init()
    {
        $this->setEnv(array(
            'HTTPS',
            'HTTP_HOST',
            'SCRIPT_NAME',
            'REQUEST_URI'
        ));
    }
}
