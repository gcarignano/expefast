<?php

namespace GatewayTests\Entities;

use \Gateway\Common\Fields;
use \Gateway\Entities\CustomData;

class CustomDataTest extends \GatewayTests\TestCase
{
    public function testConstructor()
    {
        $cd = new CustomData('cd1');
        $this->assertTrue($cd->validate());
        $this->assertEquals('cd1', $cd->getArray()[Fields::CUSTOM_DATA1]);

        $cd = new CustomData('cd1', 'cd2', 'cd3', 'cd4', 'cd5', 'site');
        foreach ([
            Fields::CUSTOM_DATA1 => 'cd1',
            Fields::CUSTOM_DATA2 => 'cd2',
            Fields::CUSTOM_DATA3 => 'cd3',
            Fields::CUSTOM_DATA4 => 'cd4',
            Fields::CUSTOM_DATA5 => 'cd5',
            Fields::SITE => 'site'
        ] as $k => $v) {
            $this->assertEquals($v, $cd->getArray()[$k]);
        }
    }

    public function testSetSite()
    {
        $cd = (new CustomData('cd1'))->setSite('site');
        $this->assertTrue($cd->validate());
        $this->assertEquals('site', $cd->getArray()[Fields::SITE]);
    }
}
