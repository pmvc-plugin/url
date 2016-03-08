<?php
# plugin
PMVC\Load::plug();
PMVC\setPlugInFolder('../');

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
}
