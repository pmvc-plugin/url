<?php
namespace PMVC\PlugIn\url;

use PHPUnit_Framework_TestCase;
use PMVC;

class UrlObjectTest extends PHPUnit_Framework_TestCase
{
    private $_plug = 'url';

    function setup()
    {
        PMVC\unplug($this->_plug);
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
            'path'=>['','path'],
            'query'=>new Query(['arg'=>'value','zzz'=>'yyy']),
            'fragment'=>'anchor'
        ],\PMVC\get($o));
        $expected = 'http://username:password@hostname:9090/path?arg=value&zzz=yyy#anchor';
        $this->assertEquals($expected,(string)$o);
    }

    function testAddQuery()
    {
        $p = PMVC\plug($this->_plug);
        $o = $p->getUrl(['query'=>'abc=def']);
        $o->query['query2']='def';
        $expected = '?abc=def&query2=def';
        $this->assertEquals($expected,(string)$o);
    }

    function testSetQuery()
    {
        $p = PMVC\plug($this->_plug);
        $o = $p->getUrl('?xxx=yyy');
        $o->set('?aaa=bbb');
        $expected = '?aaa=bbb&xxx=yyy';
        $this->assertEquals($expected,(string)$o);
    }

    function testSetPointQuery()
    {
        $p = PMVC\plug($this->_plug);
        $o = $p->getUrl('?xxx.yyy=zzz&aaa_bbb=ccc');
        $this->assertEquals('zzz',$o->query['xxx.yyy']);
        $this->assertEquals('ccc',$o->query['aaa_bbb']);
    }

    function testSetSpaceQuery()
    {
        $p = PMVC\plug($this->_plug);
        $o = $p->getUrl('localhost/path?xxx%20yyy=zzz&aaa bbb=ccc');
        $this->assertEquals('zzz',$o->query['xxx_yyy']);
        $this->assertEquals('ccc',$o->query['aaa_bbb']);
    }

    function testEmptyPath()
    {
        $p = PMVC\plug($this->_plug);
        $o = $p->getUrl(['host'=>'localhost','query'=>'aaa=bbb']);
        $expected = 'localhost/?aaa=bbb';
        $this->assertEquals($expected,(string)$o);
    }

    function testAppendPath()
    {
        $p = PMVC\plug($this->_plug);
        $o = $p->getUrl('path/');
        $o->set('path2/');
        $expected = 'path/path2/';
        $this->assertEquals($expected,(string)$o);
    }

    function testPrependPath()
    {
        $p = PMVC\plug($this->_plug);
        $o = $p->getUrl('/path/');
        $o->set('http://php.net/1/');
        $expected = 'http://php.net/1/path/';
        $this->assertEquals($expected,(string)$o);
    }

    function testSetPathWithLastSlash()
    {
        $p = PMVC\plug($this->_plug);
        $o = $p->getUrl('/path/');
        $expected = '/path/';
        $this->assertEquals($expected,(string)$o);
    }

    function testSetEmptyPathWithSlash()
    {
        $p = PMVC\plug($this->_plug);
        $expected = 'http://php.net/1/';
        $o = $p->getUrl($expected);
        $o->appendPath('');
        $this->assertEquals($expected,(string)$o);
    }

    function testReplacePath()
    {
        $p = PMVC\plug($this->_plug);
        $o = $p->getUrl('path');
        $o->path='path2';
        $expected = 'path2';
        $this->assertEquals($expected,(string)$o);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    function testSetInvalidKey()
    {
        $p = PMVC\plug($this->_plug);
        $o = $p->getUrl('path');
        $o['xxx'] = 'yyy';
    }

    /**
     * Explain with empty Scheme
     */
    function testUrlWithEmptySchemeHost()
    {
        $p = PMVC\plug($this->_plug);
        $o = $p->getUrl('//tw.yahoo.com/xxx');
        $this->assertTrue(empty($o[SCHEME]));
        $this->assertEquals('tw.yahoo.com', $o[HOST]);
        $o1 = $p->getUrl('google.com/xxx');
        $this->assertTrue(empty($o1[SCHEME]));
        $this->assertTrue(empty($o1[HOST]));
        $this->assertEquals('google.com/xxx', $o1->getPath());
        $o2 = $p->getUrl('/bing.com/xxx');
        $this->assertTrue(empty($o2[SCHEME]));
        $this->assertTrue(empty($o2[HOST]));
        $this->assertEquals('/bing.com/xxx', $o2->getPath());
    }
}
