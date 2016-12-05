<?php
namespace PMVC\PlugIn\url;

use PMVC;

use PHPUnit_Framework_TestCase;

class UrlGetPathTest extends PHPUnit_Framework_TestCase
{
    private $_plug = 'url';

    function setup()
    {
        PMVC\unplug($this->_plug);
    }

    function testRewriteRuleWithoutPT()
    {
        $oUrl = \PMVC\plug($this->_plug);
        $expected = '/path';
        $oUrl['SCRIPT_NAME'] = '';
        $oUrl['REQUEST_URI'] = $expected; 
        $actual = $oUrl->getPath();

        $this->assertEquals($expected,$actual);
    }
    function testRewriteRuleWithPT()
    {
        $oUrl = \PMVC\plug($this->_plug);
        $expected = '/path';
        $oUrl['SCRIPT_NAME'] = '/index.php';
        $oUrl['REQUEST_URI'] = $expected; 
        $actual = $oUrl->getPath();

        $this->assertEquals($expected,$actual);
    }

    function testQueryString()
    {
        $oUrl = \PMVC\plug($this->_plug);
        $php = '/xxx.php';
        $expected = '/path';
        $oUrl['SCRIPT_NAME'] = $php;
        $oUrl['REQUEST_URI'] = $php.'?'.$expected; 
        $actual = $oUrl->getPath();
        $this->assertEquals($expected,$actual);
    }

    function testPathStripQuery()
    {
        $oUrl = \PMVC\plug($this->_plug);
        $php = '/xxx.php';
        $expected = '/path';
        $oUrl['SCRIPT_NAME'] = $php;
        $oUrl['REQUEST_URI'] = $php.$expected.'?a=1&b=2';
        $actual = $oUrl->getPath();
        $this->assertEquals($expected,$actual);
    }
}