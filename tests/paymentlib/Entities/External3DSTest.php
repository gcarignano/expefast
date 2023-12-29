<?php

namespace GatewayTests\Entities;

use \Gateway\Entities\External3DS;
use \Gateway\Common\Fields;
use \Gateway\Common\ThreeDSStatus;
use \Gateway\Exceptions\ValidationException;

class External3DSTest extends \GatewayTests\TestCase
{
    public function testConstructor()
    {
        $ext3ds = new External3DS(ThreeDSStatus::$Y);
        $this->assertTrue($ext3ds->validate());
        $this->assertEquals($ext3ds->getArray()[Fields::THREEDSSTATUS], ThreeDSStatus::$Y->getValue());

        foreach ([
            'setAcsTransactionId' => Fields::ACSTRANSACTIONID,
            'setDsTransactionId' => Fields::DSTRANSACTIONID,
            'set3DSServerTransactionId' => Fields::THREEDSSERVERTRANSACTIONID,
            'set3DSVersion' => Fields::THREEDSVERSION,
            'setAuthenticationValue' => Fields::AUTHENTICATIONVALUE,
            'setXid' => Fields::XID
        ] as $k => $v) {
            $ext3ds = new External3DS(ThreeDSStatus::$Y);
            $ext3ds->$k('ABCDEF123456-=/+');
            $this->assertTrue($ext3ds->validate());
            $this->assertEquals('ABCDEF123456-=/+', $ext3ds->getArray()[$v], "Failed: $k");
        }
    }

    public function testSetECICode()
    {
        foreach ([
            '00' => [0, '0', '00'],
            '01' => [1, '1', '01'],
            '02' => [2, '2', '02'],
            '5' => [5, '5'],
            '6' => [6, '6'],
            '7' => [7, '7'],
        ] as $k => $v) {
            $ext3ds = new External3DS(ThreeDSStatus::$Y);
            foreach ($v as $inputval) {
                $ext3ds->setECICode($inputval);
                $this->assertTrue($ext3ds->validate());
                $this->assertEquals($k, $ext3ds->getArray()[Fields::ECICODE], "Failed: $inputval");
            }
        }
        $this->shouldThrowException(function () {
            $ext3ds = new External3DS(ThreeDSStatus::$Y);
            $ext3ds->setECICode('9')->validate();
        });
    }
}
