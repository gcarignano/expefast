<?php

namespace GatewayTests\Entities;

use \Gateway\Common\Fields;
use \Gateway\Entities\Address;
use \Gateway\Entities\Customer;

class CustomerTest extends \GatewayTests\TestCase
{
    private $billing;

    private $shipping;

    protected function initAddress()
    {
        $this->billing = new Address(
            "John",
            "Doe",
            "john.doe@email.com"
        );
        $this->shipping = new Address(
            "Micah",
            "Lanan",
            "micah.lanan@email.com"
        );
    }

    public function testConstructor()
    {
        $cust = new Customer();
        $this->assertTrue($cust->validate());
        $this->assertEmpty($cust->getArray());

        foreach (['', 'id'] as $v) {
            $cust = new Customer($v);
            $this->assertTrue($cust->validate());
            $this->assertEquals($v, $cust->getCustomerIdField()[Fields::CUSTOMER_ID]);
        }
        $this->initAddress();
        $cust = new Customer(null, $this->billing);
        $this->assertTrue($cust->validate());
        $this->assertIsArray($cust->getArray()[Fields::BILLINGADDRESS]);
        $this->assertIsArray($cust->getArray()[Fields::SHIPPINGADDRESS]);
        $this->assertEquals(
            array_values($cust->getArray()[Fields::BILLINGADDRESS]),
            array_values($cust->getArray()[Fields::SHIPPINGADDRESS])
        );

        $cust = new Customer(null, $this->billing, $this->shipping);
        $this->assertTrue($cust->validate());
        $this->assertIsArray($cust->getArray()[Fields::BILLINGADDRESS]);
        $this->assertIsArray($cust->getArray()[Fields::SHIPPINGADDRESS]);
        $this->assertNotEquals(
            array_values($cust->getArray()[Fields::BILLINGADDRESS]),
            array_values($cust->getArray()[Fields::SHIPPINGADDRESS])
        );
    }

    public function testDisableShipping()
    {
        $this->initAddress();
        $cust = new Customer(null, $this->billing);
        $cust->disableShipping();
        $this->assertTrue($cust->validate());
        $this->assertIsArray($cust->getArray()[Fields::BILLINGADDRESS]);
        $this->assertFalse(isset($cust->getArray()[Fields::SHIPPINGADDRESS]));
    }

    public function testSetIdRequired()
    {
        $cust = new Customer();
        $cust->setIdRequired();
        $this->assertFalse($cust->validate());

        $cust = new Customer("121213127878987287192871827917298172987198279172817298719");
        $cust->setIdRequired();
        $this->shouldThrowException(function () use ($cust) {
            $cust->validate();
        });
    }
}
