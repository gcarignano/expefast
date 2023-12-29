<?php

namespace GatewayTests\Entities;

use \Gateway\Common\Fields;
use \Gateway\Entities\DynamicDescriptor;

class DynamicDescriptorTest extends \GatewayTests\TestCase
{
    public function testConstructor()
    {
        $dd = new DynamicDescriptor();
        $this->assertTrue($dd->validate());
        $this->assertTrue(empty($dd->getArray()));

        $dd = new DynamicDescriptor('mobile');
        $this->assertTrue($dd->validate());
        $this->assertEquals('mobile', $dd->getArray()[Fields::DD][Fields::DD_MOBILE]);

        $dd = new DynamicDescriptor(null, 'correct@email.com');
        $this->assertTrue($dd->validate());
        $this->assertEquals('correct@email.com', $dd->getArray()[Fields::DD][Fields::DD_EMAIL]);

        $dd = new DynamicDescriptor(null, 'incorrectemail.com');
        $this->shouldThrowException(function () use ($dd) {
            $dd->validate();
        });

        $dd = new DynamicDescriptor(null, null, 'John doe');
        $this->assertTrue($dd->validate());
        $this->assertEquals('John doe', $dd->getArray()[Fields::DD][Fields::DD_NAME]);

        $dd = new DynamicDescriptor('mobile', 'correct@email.com', 'John doe');
        foreach ([
            Fields::DD_MOBILE => 'mobile',
            Fields::DD_EMAIL => 'correct@email.com',
            Fields::DD_NAME => 'John doe',
        ] as $k => $v) {
            $this->assertTrue($dd->validate());
            $this->assertEquals($v, $dd->getArray()[Fields::DD][$k]);
        }
    }
}
