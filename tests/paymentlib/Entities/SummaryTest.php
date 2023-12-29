<?php

namespace GatewayTests\Entities;

use \Gateway\Common\Fields;
use \Gateway\Common\Messages;
use \Gateway\Exceptions\ValidationException;
use \Gateway\Entities\Summary;

class SummarryTest extends \GatewayTests\TestCase
{
    public function testConstructor()
    {
        $summary = new Summary(
            "189.89",
            "7.90",
            "5.00"
        );
        $this->assertTrue($summary->validate());
        $this->assertIsArray($summary->getArray()[Fields::DETAILS]);
        foreach ([
            Fields::SUBTOTAL => "189.89",
            Fields::TAX => "7.90",
            Fields::SHIPPINGPRICE => "5.00"
        ] as $k => $v) {
            $this->assertEquals($v, $summary->getArray()[Fields::DETAILS][$k]);
        }

        $summary = new Summary(
            "wrongamount",
            "7.90",
            "5.00"
        );
        $this->shouldThrowException(function () use ($summary) {
            $summary->validate();
        }, Messages::SUMMARY_DETAIL, ValidationException::class);
    }

    public function testAddDiscount()
    {
        $summary = new Summary(
            "189.89",
            "7.90",
            "5.00"
        );
        $summary->addDiscount('2.30', 'UIDIO90', "Test Discount");
        $this->assertTrue($summary->validate());
        $this->assertIsArray($summary->getArray()[Fields::DISCOUNT]);

        $summary->addDiscount('wrongamount', 'UIDIO90', "Test Discount");
        $this->shouldThrowException(function () use ($summary) {
            $summary->validate();
        }, Messages::SUMMARY_DISCOUNT, ValidationException::class);
    }
}
