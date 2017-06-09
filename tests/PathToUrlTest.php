<?php

namespace PMVC\PlugIn\url;

use PMVC;
use PHPUnit_Framework_TestCase;

class PathToUrlTest extends PHPUnit_Framework_TestCase
{
    private $_plug = 'url';

    public function testPathToUrl()
    {
        $path = '/error';
        $p = \PMVC\plug($this->_plug);
        $p['REQUEST_URI'] = 'http://xxx/index.php'; 
        $p['SCRIPT_NAME'] = '/index.php';
        $expected = 'http://xxx/index.php/error';
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
