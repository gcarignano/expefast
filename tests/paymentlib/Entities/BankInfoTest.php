<?php

namespace GatewayTests\Entities;

use \Gateway\Common\Fields;
use \Gateway\Common\Messages;
use \Gateway\Entities\BankInfo;

class BankInfoTest extends \GatewayTests\TestCase
{
    public function testConstructor()
    {
        $id = 'CAEEUU0998';
        $name =  'John Doe';
        $nBic = new BankInfo($id, $name);
        $bic = new BankInfo($id);

        $this->assertTrue($nBic->validate());
        $this->assertTrue($bic->validate());

        foreach ([Fields::IBAN => $id, Fields::BANK_HOLDER => $name] as $k => $v) {
            $this->assertEquals($v, $nBic->getArray()[$k]);
        }

        $this->assertEquals($id, $bic->getArray()[Fields::BIC]);
    }

    public function testBicMaxLength()
    {
        $this->shouldThrowException(function () {
            $bank = new BankInfo('CASISUDFOIER3094859038450');
            $bank->validate();
        });
    }

    public function testBankNumberMaxLength()
    {
        $this->shouldThrowException(function () {
            $bank = new BankInfo('CASISUDFOIERIASDFIODJOFIJASDOIFJ3094859038450', 'John Doe');
            $bank->validate();
        });
    }

    public function testHolderNameMaxLength()
    {
        $this->shouldThrowException(function () {
            $bank = new BankInfo(
                'CAEEUU0998',
                'ABCDEFGHIJKLMNOPQRSTUVWXYZAAAAABCDEFGHIJKLMNOPQRSTUVWXYZBCDEF'
                . 'GHIJKLMNOPQRSTUVWXYZBCDEFGHIJKLMNOPQRSTUVWXYZBCDEFGHIJKLMNO'
                . 'PQRSTUVWXYZBCDEFGHIJKLMNOPQRSTUVWXYZ'
            );
            $bank->validate();
        });
    }
}
