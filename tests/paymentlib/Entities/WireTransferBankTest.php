<?php

namespace GatewayTests\Entities;;

use Gateway\Common\Fields;
use Gateway\Entities\WireTransferBank;

class WireTransferBankTest extends \GatewayTests\TestCase
{
    public function testConstructor()
    {
        $WTransBnk = new WireTransferBank(
            '1-2 Market Square, Navan, Ireland',
            'Test bank',
            'GTBINGLA',
            '0413503557',
            '058163711'
        );
        $this->assertTrue($WTransBnk->validate());
        $this->assertIsArray($WTransBnk->getArray());

        foreach ([Fields::WIRETRANSFER_BANK_ADDRESS => '1-2 Market Square, Navan, Ireland',
                  Fields::WIRETRANSFER_BANK_NAME => 'Test bank',
                  Fields::WIRETRANSFER_BANK_SORT_CODE => '058163711',
                  Fields::WIRETRANSFER_BANK_ACCOUNT_NUMBER => '0413503557',
                  Fields::BIC=>'GTBINGLA',
        ] as $k => $v) {
            $this->assertEquals($v, $WTransBnk->getArray()[$k]);
        }
    }
}
