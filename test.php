<?php
# plugin
PMVC\Load::plug();
PMVC\setPlugInFolder('../');

class UrlTest extends PHPUnit_Framework_TestCase
{
    function testSetEnv()
    {
        $url = PMVC\plug('url');
        $url->setEnv(array('APP_ENV'));
        $this->assertEquals('testing',$url['APP_ENV']);
    }
}
