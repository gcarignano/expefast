<?php

namespace GatewayTests\Entities;;

use Gateway\Common\Fields;;
use Gateway\Entities\WireTransferInfo;

class WireTransferInfoTest extends \GatewayTests\TestCase
{
    public function testConstructor()
    {
        $WTransBnk = new WireTransferInfo(
                    'C52AAC58-584E-4F85-B625-4DCCBC19EE05'
        );
        $this->assertTrue($WTransBnk->validate());
        $this->assertIsArray($WTransBnk->getArray());
        $this->assertEquals([Fields::REGISTRATIONID => 'C52AAC58-584E-4F85-B625-4DCCBC19EE05'],$WTransBnk->getArray());
    }
}
