<?php

namespace PMVC\PlugIn\url;

use PMVC;
use PMVC\TestCase;

class ToHttpTest extends TestCase
{
    private $_plug = 'url';

    function pmvc_setup()
    {
        PMVC\unplug($this->_plug);
    }

    function testToHttp()
    {
        $oUrl = \PMVC\plug($this->_plug);
        $s = 'www.yahoo.com';
        $actual = $oUrl->toHttp($s);
        $expected = 'http://www.yahoo.com';
        $this->assertEquals($expected, $actual);
    }

    /**
     * https://stackoverflow.com/questions/743247/types-of-urls
     */
    function testToHttpByProtocolRelative()
    {
        $oUrl = \PMVC\plug($this->_plug);
        $s = 'www.yahoo.com';
        $actual = $oUrl->toHttp($s, false);
        $expected = '//www.yahoo.com';
        $this->assertEquals($expected, $actual);
    }
}
