<?php

namespace GatewayTests\Entities;

use \Gateway\Common\Fields;
use \Gateway\Entities\Address;

class AddressTest extends \GatewayTests\TestCase
{
    protected function newAddress()
    {
        return new Address(
            'John',
            'Doe',
            'john.doe@email.com',
            '0000000000'
        );
    }

    public function testConstructor()
    {
        $addr = $this->newAddress();
        $this->assertTrue($addr->validate());
        foreach ([
            Fields::FIRSTNAME => 'John',
            Fields::LASTNAME => 'Doe',
            Fields::EMAILID => 'john.doe@email.com',
            Fields::MOBILENO => '0000000000'
        ] as $k => $v) {
            $this->assertEquals($v, $addr->getArray()[$k]);
        }

        $addr = new Address(
            'John',
            'Doe',
            'john.doe@email.com'
        );
        $this->assertTrue($addr->validate());
    }

    public function testInvalidEmail()
    {
        $this->shouldThrowException(function () {
            $address = new Address(
                'John',
                'Doe',
                'someinvalidemail.com',
                '0000000000'
            );
            $address->validate();
        });
    }

    public function testSetPostalCode()
    {
        $addr = $this->newAddress();

        $zip = "1100AA";
        $addr->setPostalCode($zip);
        $this->assertTrue($addr->validate());
        $this->assertEquals($zip, $addr->getArray()[Fields::ZIP]);

        $this->shouldThrowException(function () use ($addr) {
            $zip = "123123123123123123123123123123123";
            $addr->setPostalCode($zip);
            $addr->validate();
        });
    }

    public function testSetAddressLines()
    {
        $addr = $this->newAddress()
                     ->setAddressLine1('Some Address Line One')
                     ->setAddressLine2('Some Address Line Two');

        $this->assertTrue($addr->validate());
        $this->assertEquals('Some Address Line One', $addr->getArray()[Fields::ADDRESSLINE1]);
        $this->assertEquals('Some Address Line Two', $addr->getArray()[Fields::ADDRESSLINE2]);
    }

    public function testSetState()
    {
        $addr = $this->newAddress()
                     ->setState('Some State');

        $this->assertTrue($addr->validate());
        $this->assertEquals('Some State', $addr->getArray()[Fields::REGION]);
    }

    public function testSetCountry()
    {
        $addr = $this->newAddress()
                     ->setCountry('Some Country');

        $this->assertTrue($addr->validate());
        $this->assertEquals('Some Country', $addr->getArray()[Fields::COUNTRY]);
    }

    public function testSetCity()
    {
        $addr = $this->newAddress()
                     ->setCity('Some City');

        $this->assertTrue($addr->validate());
        $this->assertEquals('Some City', $addr->getArray()[Fields::CITY]);
    }

    public function testDisabledSipping()
    {
        $addr = $this->newAddress()
                     ->asShipping()
                     ->disable();
        $this->assertTrue($addr->validate());
        $this->assertEmpty($addr->getArray());
    }

    public function testShippingAddress()
    {
        $addr = $this->newAddress()->asShipping();
        $this->assertTrue($addr->validate());
        foreach ([
            Fields::FIRSTNAME => 'John',
            Fields::LASTNAME => 'Doe',
            Fields::EMAILID => 'john.doe@email.com',
            Fields::MOBILENO => '0000000000'
        ] as $k => $v) {
            $this->assertEquals($v, $addr->getArray()['s' . ucfirst($k)]);
        }
    }

    public function testRequiredFields()
    {
        $this->assertIsArray($this->newAddress()->requiredFields());
    }
}
