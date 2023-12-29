<?php

namespace GatewayTests\Utility;

use \Gateway\Utility\Util;

class UtilTest extends \GatewayTests\TestCase
{
    public function testGUIDFormat()
    {
        if(method_exists($this,'assertMatchesRegularExpression')){
            $this->assertMatchesRegularExpression(
                '/^\{?[A-Z0-9]{8}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{12}\}?$/',
                Util::generateGUID()
            );
        } else {
            $this->assertRegExp(
                '/^\{?[A-Z0-9]{8}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{12}\}?$/',
                Util::generateGUID()
            );
        }
    }

    public function testGUIDUnique()
    {
        $sampleSize = 10000;
        $uidStore = [];
        foreach (range(1, $sampleSize) as $x) {
            $uidStore[$x] = true;
        }
        $this->assertEquals(count($uidStore), $sampleSize);
    }
}
