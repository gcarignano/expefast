<?php

namespace GatewayTests\Entities;;

use Gateway\Common\Fields;
use Gateway\Entities\WireTransferBankEEA;

class WireTransferBankEEATest extends \GatewayTests\TestCase
{
    public function testConstructor()
    {
        $WTransBnk = new WireTransferBankEEA(
            '1-2 Market Square, Navan, Ireland',
            'Test bank',
            'GTBINGLA',
            'AT152011128161647502'
        );
        $this->assertTrue($WTransBnk->validate());
        $this->assertIsArray($WTransBnk->getArray());

        foreach ([Fields::WIRETRANSFER_BANK_ADDRESS => '1-2 Market Square, Navan, Ireland',
                  Fields::WIRETRANSFER_BANK_NAME => 'Test bank',
                  Fields::BIC => 'GTBINGLA',
                  Fields::IBAN => 'AT152011128161647502'
        ] as $k => $v) {
            $this->assertEquals($v, $WTransBnk->getArray()[$k]);
        }
    }
}
