<?php

namespace PMVC\PlugIn\url;

use PMVC;
use PHPUnit_Framework_TestCase;

class UrlTest extends PHPUnit_Framework_TestCase
{
    private $_plug = 'url';

    function setup()
    {
        PMVC\unplug($this->_plug);
    }

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
        $url->setEnv([$key]);
        $this->assertEquals($expected,$url[$key]);
    }

    function testSetEnvOverwirte()
    {
        $url = PMVC\plug($this->_plug);
        $expected = 'testing';
        $default = 'default';
        $key = 'APP_ENV';
        $url[$key] = $default;
        $_SERVER[$key] = $expected;
        $url->setEnv([$key], false);
        $this->assertEquals($default,$url[$key], 'should keep default value');
        unset($url[$key]);
        $url->setEnv([$key], false);
        $this->assertEquals($expected,$url[$key], 'should use env value');
    }

    function testRealurlWithoutScript()
    {
        $oUrl = PMVC\plug($this->_plug);
        $oUrl['REQUEST_URI'] = 'http://xxx/abc'; 
        $oUrl['SCRIPT_NAME'] = '/index.php';
        $expected = 'http://xxx';
        $actural = $oUrl->realUrl();
        $this->assertEquals($expected,$actural);
    }

    function testRealUrlWithScript()
    {
        $oUrl = PMVC\plug($this->_plug);
        $oUrl['REQUEST_URI'] = 'http://xxx/index.php/abc'; 
        $oUrl['SCRIPT_NAME'] = '/index.php';
        $expected = 'http://xxx/index.php';
        $actural = $oUrl->realUrl();
        $this->assertEquals($expected,$actural);
    }

    function testRealUrlIndexWithoutSlash()
    {
        $oUrl = PMVC\plug($this->_plug);
        $oUrl['REQUEST_URI'] = 'http://xxx/index.php'; 
        $oUrl['SCRIPT_NAME'] = '/index.php';
        $expected = 'http://xxx/index.php';
        $actural = $oUrl->realUrl();
        $this->assertEquals($expected,$actural);
    }

    /**
     * @dataProvider realUrlWithOnlyHostnameProvider
     */
    function testRealUrlWithOnlyHostname($requestUri)
    {
        $oUrl = PMVC\plug($this->_plug);
        $oUrl['REQUEST_URI'] = $requestUri; 
        $oUrl['SCRIPT_NAME'] = '/index.php';
        $oUrl['HTTP_HOST'] = 'xxx';
        $oUrl[HOST] = $oUrl->getDefaultHost();
        $expected = 'http://xxx';
        $actural = $oUrl->realUrl();
        $this->assertEquals($expected,$actural);
    }

    function realUrlWithOnlyHostnameProvider()
    {
        return [
            [
               'http://xxx' 
            ],
            [
               'http://xxx/'
            ],
            [
                'http://xxx?abc=1'
            ],
            [
                'http://xxx/?abc=2'
            ],
            [
                '/'
            ],
            [
                '/?abc=3'
            ]
        ];
    }

}
