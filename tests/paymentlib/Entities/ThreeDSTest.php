<?php

namespace GatewayTests\Entities;

use \Gateway\Entities\ThreeDS;
use \Gateway\Entities\External3DS;
use \Gateway\Common\Fields;
use \Gateway\Common\Messages;
use \Gateway\Common\ThreeDSStatus;

class ThreeDSTest extends \GatewayTests\TestCase
{
    public function testInvalidFingerprintField()
    {
        $tds = new ThreeDS(['invalid-field' => 'invalid-value']);
        $this->assertTrue($tds->validate());
        $this->assertFalse(isset($tds->getArray()[Fields::FINGERPRINT]));
    }

    public function testFingerprintField()
    {
        $arr = [
            Fields::OS => "linux",
            Fields::BROWSERACCEPTHEADER => "ACCEPT"
        ];
        $tds = new ThreeDS($arr);
        $this->assertTrue($tds->validate());
        foreach ($arr as $k => $v) {
            $this->assertEquals($v, $tds->getArray()[Fields::FINGERPRINT][$k]);
        }
    }

    public function testAddSDKFields()
    {
        $arr = [
            Fields::SDKAPPID => "sdkid",
            Fields::SDKMAXTIMEOUT => "100"
        ];
        $tds = new ThreeDS();
        $tds->addSDKFields($arr);
        $this->assertTrue($tds->validate());
        foreach ($arr as $k => $v) {
            $this->assertEquals($v, $tds->getArray()[Fields::SDK][$k]);
        }
    }

    public function testChallengeIndicator()
    {
        $tds = new ThreeDS();

        $this->shouldThrowException(
            function () use ($tds) {
                $tds->setChallengeIndicator(5);
                $tds->validate();
            },
            Messages::CHALLENGE_INDICATOR
        );

        $this->shouldThrowException(
            function () use ($tds) {
                $tds->setChallengeIndicator('hundred');
                $tds->validate();
            },
            Messages::CHALLENGE_INDICATOR
        );

        $this->shouldThrowException(
            function () use ($tds) {
                $tds->setChallengeIndicator(0);
                $tds->validate();
            },
            Messages::CHALLENGE_INDICATOR
        );

        $tds->setChallengeIndicator();
        $this->assertTrue($tds->validate());
        if(method_exists($this,'assertMatchesRegularExpression')){
            $this->assertMatchesRegularExpression('/^0[1-4]$/', $tds->getArray()[Fields::CHALLENGEINDICATOR]);
        }else{
            $this->assertRegExp('/^0[1-4]$/', $tds->getArray()[Fields::CHALLENGEINDICATOR]);
        }
        $tds->setChallengeIndicator(1);
        $this->assertTrue($tds->validate());
        $this->assertEquals('01', $tds->getArray()[Fields::CHALLENGEINDICATOR]);

        $tds->setChallengeIndicator(4);
        $this->assertTrue($tds->validate());
        $this->assertEquals('04', $tds->getArray()[Fields::CHALLENGEINDICATOR]);
    }

    public function testChallengeWindowSize()
    {
        $tds = new ThreeDS();
        $tds->setChallengeWindowSize();
        $this->assertTrue($tds->validate());
        if(method_exists($this,'assertMatchesRegularExpression')){
            $this->assertMatchesRegularExpression('/^0[1-5]$/', $tds->getArray()[Fields::CHALLENGEWINDOWSIZE]);
        }else{
            $this->assertRegExp('/^0[1-5]$/', $tds->getArray()[Fields::CHALLENGEWINDOWSIZE]);
        }

        $tds->setChallengeWindowSize(1);
        $this->assertTrue($tds->validate());
        $this->assertEquals('01', $tds->getArray()[Fields::CHALLENGEWINDOWSIZE]);

        $tds->setChallengeWindowSize(5);
        $this->assertTrue($tds->validate());
        $this->assertEquals('05', $tds->getArray()[Fields::CHALLENGEWINDOWSIZE]);

        $this->shouldThrowException(
            function () use ($tds) {
                $tds->setChallengeWindowSize(6);
                $tds->validate();
            },
            Messages::CHALLENGE_WINSIZE
        );

        $this->shouldThrowException(
            function () use ($tds) {
                $tds->setChallengeWindowSize(0);
                $tds->validate();
            },
            Messages::CHALLENGE_WINSIZE
        );
    }

    public function testExemptions()
    {
        $tds = new ThreeDS();
        foreach ($tds->getArray()[Fields::EXEMPTIONS] as $k => $v) {
            $this->assertFalse($v, "Failed for: $k");
        }

        foreach ([
            'exemptLowValue' => Fields::LOWVALUE,
            'exemptTRA' => Fields::TRA,
            'exemptTrustedBeneficiary' => Fields::TRUSTEDBENEFICIARY,
            'exemptSecureCorporatePayment' => Fields::SECURECORPORATEPAYMENT,
            'exemptRecurringMITOther' => Fields::RECURRING_EXEMPTION_OTHER,
            'exemptRecurringMITSameAmount' => Fields::RECURRING_EXEMPTION_SAMEAMOUNT,
            'exemptDelegatedAuthentication' => Fields::DELEGATEDAUTHENTICATION
        ] as $k => $v) {
            $tds = new ThreeDS();
            $tds->$k();
            $this->assertTrue($tds->validate());
            $this->assertTrue($tds->getArray()[Fields::EXEMPTIONS][$v], "Failed: $k");
        }

        foreach ([100, 'one two', 10.10] as $v) {
            $tds = new ThreeDS();
            $tds->exemptVMID($v);
            $this->assertEquals($tds->getArray()[Fields::EXEMPTIONS][Fields::VMID], $v);
        }
    }

    public function testAddExternal3DS()
    {
        $tds = new ThreeDS();
        $ext3ds = new External3DS(ThreeDSStatus::$Y);
        $this->assertFalse($tds->hasExternal3DS());

        $tds->addExternal3DS($ext3ds);
        $this->assertTrue($tds->hasExternal3DS());
        $this->assertArrayHasKey(Fields::EXTERNALTHREEDS, $tds->getArray());
        $this->assertEquals($ext3ds->getArray(), $tds->getArray()[Fields::EXTERNALTHREEDS]);
    }
}
