<?php

namespace GatewayTests;

class TestCase extends \PHPUnit\Framework\TestCase
{
    public function shouldThrowException($func, $msg = null, $type = null)
    {
        try {
            $func();
        } catch (\Exception $e) {
            if ($msg) {
                $this->assertEquals($e->getMessage(), $msg);
            }
            if ($type) {
                $this->assertInstanceOf($type, $e);
            }
            return;
        }

        $this->fail("Expected an exception: $msg");
    }
}
