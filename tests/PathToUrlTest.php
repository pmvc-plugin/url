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
        $p = \PMVC\plug($this->_plug);
        $p['REQUEST_URI'] = '/index.php/path1/path2'; 
        $p['SCRIPT_NAME'] = '/index.php';
        $p['HTTP_HOST'] = 'xxx';
        \PMVC\option('set','realUrl',$p->realUrl());
        $expected = 'http://xxx/index.php/error';
        $actural = $p->pathToUrl($path);
        $this->assertEquals($expected,$actural);
    }


    public function testPathToUrlWithPort() {
        $path = '/error';
        $p = \PMVC\plug($this->_plug);
        $p['REQUEST_URI'] = '/index.php/path1/path2'; 
        $p['SCRIPT_NAME'] = '/index.php';
        $p['HTTP_HOST'] = 'xxx:8888';
        \PMVC\option('set','realUrl',$p->realUrl());
        $expected = 'http://xxx:8888/index.php/error';
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
