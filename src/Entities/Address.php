<?php
/**
 * Copyright 2023 Connfido BV. All rights reserved.
 * Distribution of this software without explicit written consent from Newgen is
 * strictly prohibited. No part of this software must not be reverse engineered,
 * copied, reproduced or modified.
 */

namespace Gateway\Entities;

use Gateway\Utility\Validator;
use Gateway\Common\Fields;

/**
 * Address
 */
final class Address extends AbstractEntity
{
    /**
     * First Name
     *
     * @var string
     */
    protected $firstName;

    /**
     * Last Name
     *
     * @var string
     */
    protected $lastName;

    /**
     * Address or Street Line 1
     *
     * @var string
     */
    protected $addressLine1;

    /**
     * Address or Street Line 2
     *
     * @var string
     */
    protected $addressLine2;

    /**
     * City
     *
     * @var string
     */
    protected $city;

    /**
     * State or Region
     *
     * @var string
     */
    protected $state;

    /**
     * Country
     *
     * @var string
     */
    protected $country;

    /**
     * ZIP or Postal Code
     *
     * @var string
     */
    protected $postalCode;

    /**
     * Email Address
     *
     * @var string
     */
    protected $email;

    /**
     * Phone Number
     *
     * @var string
     */
    protected $phone;

    /**
     * @var boolean
     */
    private $isShipping = false;

    /**
     * @var boolean
     */
    private $disable = false;

    /**
     * @param string $firstName
     * @param string $lastName
     * @param string $email
     * @param string $phone
     */
    public function __construct($firstName, $lastName, $email, $phone = null)
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->phone = $phone;
    }

    /**
     * @inheritdoc
     */
    protected $required = [
        "email"
    ];

    /**
     * @inheritdoc
     */
    public function getArray()
    {
        if ($this->disable && $this->isShipping) {
            return [];
        }

        $_data = [
            Fields::FIRSTNAME => $this->firstName,
            Fields::LASTNAME => $this->lastName,
            Fields::EMAILID => $this->email
        ];
        if (!empty($this->phone)) {
            $_data[Fields::MOBILENO] = $this->phone;
        }
        if (!empty($this->addressLine1)) {
            $_data[Fields::ADDRESSLINE1] = $this->addressLine1;
        }
        if (!empty($this->addressLine2)) {
            $_data[Fields::ADDRESSLINE2] = $this->addressLine2;
        }
        if (!empty($this->city)) {
            $_data[Fields::CITY] = $this->city;
        }
        if (!empty($this->state)) {
            $_data[Fields::REGION] = $this->state;
        }
        if (!empty($this->country)) {
            $_data[Fields::COUNTRY] = $this->country;
        }
        if (!empty($this->postalCode)) {
            $_data[Fields::ZIP] = $this->postalCode;
        }
        return $this->billingToShipping($_data);
    }

    /**
     * @inheritdoc
     */
    public function validate()
    {
        return (parent::validate()
            && Validator::isUniAlnumSpecial($this->firstName, 100, true)
            && Validator::isUniAlnumSpecial($this->lastName, 100, true)
            && Validator::isEmail($this->email)
            && Validator::isAllSpecial($this->city, 100, true)
            && Validator::isAllSpecial($this->state, 100, true)
            && Validator::isAllSpecial($this->country, 100, true)
            && Validator::isAlnumSpecial($this->postalCode, 32, true));
    }

    /**
     * Set the address as a shipping address
     *
     * @return \Gateway\Entities\Address
     */
    public function asShipping()
    {
        $this->isShipping = true;
        return $this;
    }

    /**
     * Disable address output
     *
     * @return \Gateway\Entities\Address
     */
    public function disable()
    {
        $this->disable = true;
        return $this;
    }

    /**
     * Converts billing address to shipping address
     *
     * @param array $data
     * @return array
     */
    private function billingToShipping(array $data)
    {
        if (!$this->isShipping) {
            return $data;
        }
        $res = [];
        foreach ($data as $key => $value) {
            $res['s' . ucfirst($key)] = $value;
        }
        return $res;
    }

    /**
     * Setter for Address or Street Line 1
     *
     * @param string $param
     * @return \Gateway\Entities\Address
     */
    public function setAddressLine1($param)
    {
        $this->addressLine1 = $param;
        return $this;
    }

    /**
     * Setter for Address or Street Line 2
     *
     * @param string $param
     * @return \Gateway\Entities\Address
     */
    public function setAddressLine2($param)
    {
        $this->addressLine2 = $param;
        return $this;
    }

    /**
     * Setter for City
     *
     * @param string $param
     * @return \Gateway\Entities\Address
     */
    public function setCity($param)
    {
        $this->city = $param;
        return $this;
    }

    /**
     * Setter for State or Region
     *
     * @param string $param
     * @return \Gateway\Entities\Address
     */
    public function setState($param)
    {
        $this->state = $param;
        return $this;
    }

    /**
     * Setter for Country
     *
     * @param string $param
     * @return \Gateway\Entities\Address
     */
    public function setCountry($param)
    {
        $this->country = $param;
        return $this;
    }

    /**
     * Setter for ZIP or Postal Code
     *
     * @param string $param
     * @return \Gateway\Entities\Address
     */
    public function setPostalCode($param)
    {
        $this->postalCode = $param;
        return $this;
    }
}
