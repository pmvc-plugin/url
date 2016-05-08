<?php
# plugin
PMVC\Load::plug();
PMVC\addPlugInFolders(['../']);

class UrlTest extends PHPUnit_Framework_TestCase
{
    private $_plug = 'url';
    function testPlugin()
    {
        ob_start();
        print_r(PMVC\plug($this->_plug));
        $output = ob_get_contents();
        ob_end_clean();
        $this->assertContains($this->_plug,$output);
    }
    function testSetEnv()
    {
        $url = PMVC\plug($this->_plug);
        $expected = 'testing';
        $key = 'APP_ENV';
        $_SERVER[$key] = $expected;
        $url->setEnv(array($key));
        $this->assertEquals($expected,$url[$key]);
    }

    function testUrlObject()
    {
        $p = PMVC\plug($this->_plug);
        $url = 'http://username:password@hostname:9090/path?zzz=yyy&arg=value#anchor';
        $o = $p->getUrl($url);
        $this->assertEquals([
            'scheme'=>'http',
            'host'=>'hostname',
            'port'=>9090,
            'user'=>'username',
            'pass'=>'password',
            'path'=>['path'],
            'query'=>['arg'=>'value','zzz'=>'yyy'],
            'fragment'=>'anchor'
        ],\PMVC\get($o));
        $expected = 'http://username:password@hostname:9090/path?arg=value&zzz=yyy#anchor';
        $this->assertEquals($expected,(string)$o);
    }
}
