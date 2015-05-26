<?php
${_INIT_CONFIG}[_CLASS] = '_PMVC_PLUG_URL';

class _PMVC_PLUG_URL extends PMVC\PLUGIN
{

    /**
     * keep value to check now use http or https
     * @var string
     */
     private $protocol=null;

    /**
     * set env
     */
     public function setEnv($arr)
     {
        foreach($arr as $key){
            $this->set($key,getenv($key));
        }
     }
    
    /**
     * $att['url']
     * $att['action']
     * $att['del']
     * $att['separator']
     */
    function getUrl($att=array())
    {
        $myUrl = $this->actionToUrl($att['action'],$att['url']);
        $separator = $this->getSeparator($att['separator']);
        $urls = parent::get(); //don't want effect $this->url
        if(is_array($att['del'])){
	    foreach($att['del'] as $k){ unset($urls[$k]); }
        }
        if(PMVC\n($urls)){
            return $myUrl.$seprator.$this->arrayToUrl($urls,null,$seprator);
        }else{
            return $myUrl;
        }
    }


    function getRunPhp()
    {
        return basename($this->get('SCRIPT_NAME'));
    }


    /**
     * Get path information from the environment.
     * @access public
     * @return string
     */
    function getPathInfo()
    {
        $uri = $this->get('REQUEST_URI');
        if( false===strpos($uri,$this->get('SCRIPT_NAME')) ){
            $start = strpos($uri,'?')+1;
        }else{
            $start = strpos($uri,$this->getRunPhp())+strlen($this->getRunPhp());
        }
        $s = substr($uri,$start,strlen($uri));
        if(false!==strpos($s,'?')){
            $s = substr($s,0,strpos($s,'?'));
        }
        if(!$s){
            $s='/';
        }
        return $s;
    }


    function actionToUrl($action,$url=null)
    {
        $routing = PMVC\getOption(_ROUTING);
        if($routing){
            return PMVC\plug($routing)->actionToUrl($action,$url);
        }

    }

    function realUrl()
    {
        $args =& func_get_args();
        $url = call_user_func_array(array($this,'actionToUrl'),$args);
        return $this->toHttp($url);
    }

   /**
    * get http or https
    */ 
    function getProtocol()
    {
        if(!is_null($this->protocol)){
            return $this->protocol;
        }else{
            $this->protocol = ( 'on'!=$this->get('HTTPS') ) ? 'http' : 'https';
            return $this->protocol;
        }
    }


    function tohttp($url,$type=null)
    {
        if(!preg_match('/^http/i',$url)){
            if(is_null($type)){
                $type= $this->getProtocol();
            }
            $url = $type.'://'.$this->get('HTTP_HOST').$url;
        }
        return $url;
    }

    function arrayToUrl($arr,$parent=null,$seprator=null,$isEncode=true)
    {
        if(!is_array($arr))return null;
        $seprator = $this->getSeparator($seprator);
        foreach($arr as $k=>$v){
                $newParent = $parent;
                $newParent[] = $k;
                if(is_array($v)){
                        $return.=(($return)?$seprator:'').$this->arrayToUrl($v,$newParent,$seprator,$isEncode);
                        continue;
                }
                if(empty($parent)){
                        $newKey = $this->getEncode($k,$isEncode);
                }else{
                        $newKey = $this->getEncode($newParent[0],$isEncode);
                        unset($newParent[0]);
                        foreach($newParent as $v1){
                                $newKey .='['.$this->getEncode($v1,$isEncode).']';
                        }
                }
                $return.=(($return)?$seprator:'').$newKey.'='.$this->getEncode($v,$isEncode);
        }
        return $return;
    }

    function getEncode($string,$isEncode)
    {
        if($isEncode){
                return urlencode($string);
        }else{
                return $string;
        }
    }

    function getSeparator($seprator=null)
    {
       	return (is_null($seprator))?_URL_SPLIT:$seprator;
    }


}
