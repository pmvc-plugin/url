<?php
# plugin
include_once('vendor/pmvc/pmvc/include_plug.php');
PMVC\setPlugInFolder('../');

class UrlTest extends PHPUnit_Framework_TestCase
{
    function testSetEnv()
    {
        $url = PMVC\plug('url');
        $url->setEnv(array('APP_ENV'));
        $this->assertEquals('testing',$url->get('APP_ENV'));
    }
}
