<?php

namespace GatewayTests\Exceptins;

use \Gateway\Entities\Address;

class NewgenExceptionTest extends \GatewayTests\TestCase
{
    public function testToString()
    {
        $this->shouldThrowException(function () {
            $addr = new Address("john", "doe", "wrongemail");
            try {
                $addr->validate();
            } catch (\Exception $e) {
                $this->assertEquals('', sprintf("%s", $e));
            }
        });
    }
}
