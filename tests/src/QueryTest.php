<?php
namespace PMVC\PlugIn\url;

use PMVC;
use PMVC\TestCase;

class QueryTest extends TestCase
{
    private $_plug = 'url';

    function pmvc_setup()
    {
        PMVC\replug($this->_plug);
    }

    function testBaseQuery()
    {
        $a = 'a=1&b=2';
        $query = new Query($a);
        $this->assertEquals(
            [
                'a' => 1,
                'b' => 2,
            ],
            \PMVC\get($query)
        );
        $this->assertEquals($a, (string) $query);
    }

    function testSequenceKey()
    {
        $query = new Query();
        $query->set('0=a');
        $query->set('0=b');
        $this->assertEquals(
            [
                0 => 'b',
            ],
            \PMVC\get($query)
        );
    }

    function testParsePointQuery()
    {
        $o = Query::parse_str('xxx.yyy=zzz&aaa_bbb=ccc');
        $this->assertEquals('zzz', $o['xxx.yyy']);
        $this->assertEquals('ccc', $o['aaa_bbb']);
    }

    function testSetSpaceQuery()
    {
        $o = Query::parse_str('xxx%20yyy=zzz&aaa bbb=ccc');
        $this->assertEquals('zzz', $o['xxx_yyy']);
        $this->assertEquals('ccc', $o['aaa_bbb']);
    }

    function testSpaceAndUnderscoresHasSameName()
    {
        // the last item will overwrite previous one.
        $o = Query::parse_str('aaa_bbb=c1&aaa bbb=c2');
        $this->assertEquals($o, ['aaa_bbb' => 'c2']);
    }
}
