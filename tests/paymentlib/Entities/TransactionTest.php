<?php

namespace GatewayTests\Entities;

use \Gateway\Common\Currency;
use \Gateway\Common\Fields;
use \Gateway\Entities\Transaction;

class TransactionTest extends \GatewayTests\TestCase
{
    public function testConstructor()
    {
        $txn = new Transaction('000009090081', '90.89', Currency::$EUR);
        $this->assertTrue($txn->validate());
        $this->assertIsArray($txn->getArray());
        foreach ([
            Fields::TXN_REFERENCE => '000009090081',
            Fields::TXN_AMOUNT => '90.89',
            Fields::CURRENCYCODE => Currency::$EUR->getValue()
        ] as $k => $v) {
            $this->assertEquals($v, $txn->getArray()[$k]);
        }

        $this->shouldThrowException(function () {
            $txn = new Transaction('000009090081', '90.89 EUR', Currency::$EUR);
            $txn->validate();
        });
    }

    public function testAsApp()
    {
        $txn = new Transaction('000009090081', '90.89', Currency::$EUR);
        $this->assertTrue($txn->validate());
        $this->assertIsArray($txn->getArray());
        $this->assertFalse($txn->getArray()[Fields::IS_APP]);

        $txn->asApp();
        $this->assertTrue($txn->validate());
        $this->assertIsArray($txn->getArray());
        $this->assertTrue($txn->getArray()[Fields::IS_APP]);
    }
}
