<?php

namespace GatewayTests\Entities;

use \Gateway\Common\Fields;
use \Gateway\Common\Currency;
use \Gateway\Common\InstallmentPeriod;
use \Gateway\Common\InstallmentType;
use \Gateway\Entities\Installments;

class InstallmentsTest extends \GatewayTests\TestCase
{
    public function testConstructor()
    {
        $inst = new Installments(
            Currency::$EUR,
            '100.95',
            InstallmentPeriod::$DAY,
            '10',
            '20',
            '30',
            InstallmentType::$REGULAR
        );
        $this->assertTrue($inst->validate());
        $this->assertIsArray($inst->getArray());
        foreach ([
            Fields::CURRENCYCODE => Currency::$EUR->getValue(),
            Fields::SUB_AMOUNT => '100.95',
            Fields::SUB_PERIOD => InstallmentPeriod::$DAY->getValue(),
            Fields::SUB_FREQ => '10',
            Fields::SUB_INSTALLMENTS_TOTAL => '20',
            Fields::SUB_SEQUENCE => '30',
            Fields::SUB_TYPE => InstallmentType::$REGULAR->getValue()
        ] as $k => $v) {
            $this->assertEquals($v, $inst->getArray()[0][$k]);
        }
    }

    public function testAddInstallment()
    {
        $inst = new Installments(
            Currency::$EUR,
            '100.95',
            InstallmentPeriod::$DAY,
            '10',
            '20',
            '30',
            InstallmentType::$REGULAR
        );
        $inst->addInstallment(
            Currency::$USD,
            '95.10',
            InstallmentPeriod::$WEEK,
            '30',
            '10',
            '20',
            InstallmentType::$TRIAL
        );
        $this->assertTrue($inst->validate());
        $this->assertIsArray($inst->getArray());
        $this->assertEquals(2, count($inst->getArray()), "Expected number of installments missing");
        foreach ([
            Fields::CURRENCYCODE => Currency::$USD->getValue(),
            Fields::SUB_AMOUNT => '95.10',
            Fields::SUB_PERIOD => InstallmentPeriod::$WEEK->getValue(),
            Fields::SUB_FREQ => '30',
            Fields::SUB_INSTALLMENTS_TOTAL => '10',
            Fields::SUB_SEQUENCE => '20',
            Fields::SUB_TYPE => InstallmentType::$TRIAL->getValue()
        ] as $k => $v) {
            $this->assertEquals($v, $inst->getArray()[1][$k]);
        }
    }
}
