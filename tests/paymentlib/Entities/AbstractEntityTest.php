<?php

namespace GatewayTests\Entities;

use \Gateway\Entities\Url;

class AbstractEntityTest extends \GatewayTests\TestCase
{
    public function testRequiredFields()
    {
        $addr = new Url('://example.com');
        $this->assertIsArray($addr->requiredFields());
    }
}
