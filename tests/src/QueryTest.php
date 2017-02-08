<?php
namespace PMVC\PlugIn\url;

use PHPUnit_Framework_TestCase;
use PMVC;

class QueryTest extends PHPUnit_Framework_TestCase
{
    private $_plug = 'url';

    function setup()
    {
        PMVC\plug($this->_plug, [\PMVC\PAUSE=>true]);
    }

    function testBaseQuery()
    {
        $a = 'a=1&b=2';
        $query = new Query($a);
        $this->assertEquals([
            'a'=>1,
            'b'=>2
        ], \PMVC\get($query));
        $this->assertEquals($a, (string)$query);
    }

    function testSequenceKey()
    {
        $query = new Query();
        $query->set('0=a');
        $query->set('0=b');
        $this->assertEquals([
            0=>'b',
        ], \PMVC\get($query));

    }
}
