<?php

namespace PMVC\PlugIn\url;

use PMVC;
use PMVC\TestCase;

class PathToUrlTest extends TestCase
{
    private $_plug = 'url';


    public function pmvc_setup()
    {
        PMVC\unplug($this->_plug);
    }

    public function testPathToUrl()
    {
        $path = '/error';
        $config = [
            'REQUEST_URI' => '/index.php/path1/path2',
            'SCRIPT_NAME' => '/index.php',
            'HTTP_HOST' => 'xxx',
        ]; 
        $p = \PMVC\plug($this->_plug, $config);
        \PMVC\option('set','realUrl',$p->realUrl());
        $expected = 'http://xxx/index.php/error';
        $actural = $p->pathToUrl($path);
        $this->assertEquals($expected,$actural);
    }


    public function testPathToUrlWithPort() {
        $path = '/error';
        $config = [
            'REQUEST_URI' => '/index.php/path1/path2',
            'SCRIPT_NAME' => '/index.php',
            'HTTP_HOST' => 'xxx:8888',
        ]; 
        $p = \PMVC\plug($this->_plug, $config);
        \PMVC\option('set','realUrl',$p->realUrl());
        $expected = 'http://xxx:8888/index.php/error';
        $actural = $p->pathToUrl($path);
        $this->assertEquals($expected,$actural);
    }

    public function testPathToUrlWithPort80() {
        $path = '/error';
        $config = [
            'REQUEST_URI' => '/index.php/path1/path2',
            'SCRIPT_NAME' => '/index.php',
            'HTTP_HOST' => 'xxx:80',
        ]; 
        $p = \PMVC\plug($this->_plug, $config);
        \PMVC\option('set','realUrl',$p->realUrl());
        $expected = 'http://xxx/index.php/error';
        $actural = $p->pathToUrl($path);
        $this->assertEquals($expected,$actural);
    }

    public function testPathToUrlWithPort443() {
        $path = '/error';
        $config = [
            'REQUEST_URI' => '/index.php/path1/path2',
            'SCRIPT_NAME' => '/index.php',
            'HTTP_HOST' => 'xxx:443',
        ]; 
        $p = \PMVC\plug($this->_plug, $config);
        \PMVC\option('set','realUrl',$p->realUrl());
        $expected = 'https://xxx/index.php/error';
        $actural = $p->pathToUrl($path);
        $this->assertEquals($expected,$actural);
    }

    public function testUrlToUrl()
    {
        $path = 'http://xxx/index.php/error';
        $p = \PMVC\plug($this->_plug);
        $actural = $p->pathToUrl($path);
        $this->assertEquals($path,$actural);
    }
}
