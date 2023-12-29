<?php

namespace GatewayTests\Entities;

use \Gateway\Common\Fields;
use \Gateway\Common\GatewayConstants;
use \Gateway\Common\Messages;
use \Gateway\Common\PaymentMode;
use \Gateway\Common\CardTypes;
use \Gateway\Common\ThreeDSStatus;
use \Gateway\Entities\BankInfo;
use \Gateway\Entities\CardInfo;
use \Gateway\Entities\External3DS;
use \Gateway\Entities\PaymentMethod;
use \Gateway\Entities\ThreeDS;

class PaymentMethodTest extends \GatewayTests\TestCase
{
    public function testConstructor()
    {
        $pm = new PaymentMethod();
        $this->assertTrue($pm->validate());
        $this->assertIsArray($pm->getArray());
        $this->assertEmpty($pm->getArray());

        $pm = new PaymentMethod(PaymentMode::$CREDITCARD, CardTypes::$VISA);
        $this->assertTrue($pm->validate());
        $this->assertIsArray($pm->getArray());
        $this->assertEquals(PaymentMode::$CREDITCARD->getValue(), $pm->getArray()[Fields::PAYMENT_MODE]);
        $this->assertEquals(CardTypes::$VISA->getValue(), $pm->getArray()[Fields::CARDTYPE]);
    }

    public function testGetReqAuthSignature()
    {
        $pm = new PaymentMethod();
        $this->assertEquals(GatewayConstants::PAYMENT_TYPE_HPP, $pm->getReqAuthSignature());
        $this->assertEquals(GatewayConstants::PAYMENT_TYPE_PAYOUT, $pm->asPayout()->getReqAuthSignature());
        $this->assertTrue($pm->validate());
        $this->assertIsArray($pm->getArray());

        $pm = new PaymentMethod(PaymentMode::$INTERAC);
        $this->assertEquals(GatewayConstants::PAYMENT_TYPE_PLUGIN, $pm->getReqAuthSignature());
        $this->assertEquals(GatewayConstants::PAYMENT_TYPE_WHPP_PAYOUT, $pm->asPayout()->getReqAuthSignature());
        $this->assertTrue($pm->validate());
        $this->assertIsArray($pm->getArray());

        $pm = new PaymentMethod(PaymentMode::$CREDITCARD);
        $cardInfo = new CardInfo(
            '679028738917',
            'John Doe',
            '111',
            '2022',
            '03',
            true
        );
        $pm->addWhppInfo($cardInfo);
        $this->assertEquals(GatewayConstants::PAYMENT_TYPE_WITHOUT_HPP, $pm->getReqAuthSignature());
        $this->assertEquals(GatewayConstants::PAYMENT_TYPE_WHPP_PAYOUT, $pm->asPayout()->getReqAuthSignature());
        $this->assertTrue($pm->validate());
        $this->assertIsArray($pm->getArray());
    }

    public function testAddWhppInfo()
    {
        $pm = new PaymentMethod(PaymentMode::$SEPA);
        $bankInfo = new BankInfo('CAEEUU0998', 'John Doe');

        $pm->addWhppInfo($bankInfo);
        $this->assertTrue($pm->validate());
        $this->assertIsArray($pm->getArray()[Fields::PAYMENT_DETAIL]);
        $this->assertTrue($pm->isWHPP());

        $pm = new PaymentMethod(PaymentMode::$CREDITCARD);
        $cardInfo = new CardInfo(
            '679028738917',
            'John Doe',
            '111',
            '2022',
            '03',
            true
        );
        $pm->addWhppInfo($cardInfo);
        $this->assertTrue($pm->validate());
        $this->assertIsArray($pm->getArray()[Fields::PAYMENT_DETAIL]);
        $this->assertTrue($pm->isWHPP());

        $cardInfo = new CardInfo('898982918298192819280129102810928091820182981082120182', 'holder name');
        $pm->addWhppInfo($cardInfo);
        $this->shouldThrowException(function () use ($pm) {
            $pm->validate();
        });
    }

    public function testAsPayout()
    {
        $pm = (new PaymentMethod(PaymentMode::$CREDITCARD))->asPayout();
        $this->assertTrue($pm->validate());
        $this->assertIsArray($pm->getArray());

        $pm = (new PaymentMethod(PaymentMode::$BCMC))->asPayout();
        $this->shouldThrowException(function () use ($pm) {
            $pm->validate();
        }, Messages::NO_PAYOUT);
    }

    public function testAdd3DS()
    {
        $pm = new PaymentMethod();
        $pm->add3DS(new ThreeDS());
        $this->assertTrue($pm->validate());
        $this->assertIsArray($pm->getArray()[Fields::THREE_DS]);

        $pm = new PaymentMethod(PaymentMode::$CREDITCARD);
        $cardInfo = new CardInfo(
            '679028738917',
            'John Doe',
            '111',
            '2022',
            '03',
            true
        );
        $pm->addWhppInfo($cardInfo);
        $pm->add3DS(new ThreeDS());
        $this->assertTrue($pm->validate());
        $this->assertIsArray($pm->getArray()[Fields::THREE_DS]);

        $pm = new PaymentMethod(PaymentMode::$SEPA);
        $pm->add3DS(new ThreeDS());
        $this->shouldThrowException(function () use ($pm) {
            $pm->validate();
        }, Messages::THREE_DS_NON_CARD);
    }

    public function testExt3DS()
    {
        $pm = new PaymentMethod();
        $tds = new ThreeDS();
        $tds->addExternal3DS(new External3DS(ThreeDSStatus::$Y));
        $this->assertFalse(isset($pm->getArray()[Fields::ALLOW3D]));

        $pm->add3DS($tds);
        $this->assertFalse($pm->getArray()[Fields::ALLOW3D]);
        $this->shouldThrowException(function () use ($pm) {
            $pm->validate();
        }, Messages::THREE_DS_EXT3DS);
    }

    public function testAllowDisallow3D()
    {
        $pm = new PaymentMethod();
        $pm->allow3D();
        $this->assertTrue($pm->validate());
        $this->assertIsArray($pm->getArray());
        $this->assertTrue($pm->getArray()[Fields::ALLOW3D]);

        $pm->disallow3D();
        $this->assertTrue($pm->validate());
        $this->assertIsArray($pm->getArray());
        $this->assertFalse($pm->getArray()[Fields::ALLOW3D]);
    }
}
