<?php

namespace GatewayTests\Common;

use \Gateway\Common\Banks;
use \Gateway\Common\CardTypes;
use \Gateway\Common\Columns;
use \Gateway\Common\Currency;
use \Gateway\Common\Events;
use \Gateway\Common\InstallmentPeriod;
use \Gateway\Common\InstallmentType;
use \Gateway\Common\Locale;
use \Gateway\Common\NotificationChannel;
use \Gateway\Common\PaymentMode;
use \Gateway\Common\Report;
use \Gateway\Common\SortOrder;
use \Gateway\Common\ThreeDSStatus;

class ConstantObjectTest extends \GatewayTests\TestCase
{
    public function internalTester($tClass, $arbFunc = null)
    {
        $rClass = new \ReflectionClass($tClass);
        $staticProperties = $rClass->getStaticProperties();

        foreach ($staticProperties as $propertyName => $value) {
            $this->assertInstanceOf(
                $tClass,
                $value,
                "$propertyName is not initialized in $tClass"
            );
            $this->assertIsString($value->getValue());
            if (is_callable($arbFunc)) {
                $arbFunc($value);
            }
        }
    }

    public function testCardTypes()
    {
        $this->internalTester(CardTypes::class);
    }

    public function testBankTypes()
    {
        $this->internalTester(Banks::class);
    }

    public function testColumns()
    {
        $this->internalTester(Columns::class);
    }

    public function testCurrency()
    {
        $this->internalTester(Currency::class);
    }

    public function testEvents()
    {
        $this->internalTester(Events::class);
    }

    public function testInstallmentPeriod()
    {
        $this->internalTester(InstallmentPeriod::class);
    }

    public function testInstallmentType()
    {
        $this->internalTester(InstallmentType::class);
    }

    public function testLocale()
    {
        $this->internalTester(Locale::class);
    }

    public function testNotificationChannel()
    {
        $this->internalTester(NotificationChannel::class);
    }

    public function testPaymentMode()
    {
        $this->internalTester(PaymentMode::class, function ($obj) {
            $this->assertIsBool($obj->isCardMode());
            $this->assertIsBool($obj->canHaveType());
            $this->assertIsBool($obj->isPayoutMode());
        });
    }

    public function testReport()
    {
        $this->internalTester(Report::class);
    }

    public function testSortOrder()
    {
        $this->internalTester(SortOrder::class);
    }

    public function testThreeDSStatus()
    {
        $this->internalTester(ThreeDSStatus::class);
    }
}
