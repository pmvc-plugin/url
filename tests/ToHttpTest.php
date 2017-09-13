<?php

namespace PMVC\PlugIn\url;

use PMVC;
use PHPUnit_Framework_TestCase;

class ToHttpTest extends PHPUnit_Framework_TestCase
{
    private $_plug = 'url';

    function setup()
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
