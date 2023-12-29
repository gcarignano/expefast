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
use Gateway\Common\Messages;
use Gateway\Exceptions\ValidationException;

/**
 * Customer
 *
 * @see Gateway\Entities\AbstractEntity
 * @final
 */
final class Customer extends AbstractEntity
{
    /**
     * Billing Address
     *
     * @var \Gateway\Entities\Address
     */
    protected $billingAddress;

    /**
     * Shipping Address
     *
     * @var \Gateway\Entities\Address
     */
    protected $shippingAddress;

    /**
     * IP Address
     *
     * @var string
     */
    protected $ipAddress;

    /**
     * Birth Year
     *
     * @var string
     */
    protected $year;

    /**
     * Birth Month
     *
     * @var string
     */
    protected $month;

    /**
     * Birth Day
     *
     * @var string
     */
    protected $day;
    /**
     * @var boolean
     */
    protected $addressRequired = false;

    /**
     * @var boolean
     */
    protected $idRequired = false;

    /**
     * @param string $customerId
     * @param \Gateway\Entities\Address $billingAddress
     * @param \Gateway\Entities\Address $shippingAddress
     */
    public function __construct(
        $customerId = null,
        Address $billingAddress = null,
        Address $shippingAddress = null
    ) {
        parent::__construct($customerId);
        $this->billingAddress = $billingAddress;
        $this->shippingAddress = ($billingAddress && !$shippingAddress) ?  clone $billingAddress : $shippingAddress;
        if ($billingAddress || $shippingAddress) {
            $this->setAddressRequired();
        }
    }

    /**
     * @inheritdoc
     */
    public function getArray()
    {
        $data = [];
        if ($this->addressRequired) {
            $data = [Fields::BILLINGADDRESS => $this->billingAddress->getArray()];
            $shipArray = $this->shippingAddress->asShipping()->getArray();
            if ($shipArray) {
                $data[Fields::SHIPPINGADDRESS] = $shipArray;
            }
        }
        
        if ($this->ipAddress) {
            $data[Fields::IP_ADDRESS] = $this->ipAddress;
        }

        if (
            $this->year !== null
            && $this->month !== null
            && $this->day !== null
        ) {
            $data[Fields::DOB] = $this->year . '-' . $this->month . '-' . $this->day;
        }
        return $data;
    }

    /**
     * @return array
     */
    public function getCustomerIdField()
    {
        return [Fields::CUSTOMER_ID => trim($this->getId())];
    }

    /**
     * Set address to be a required field
     *
     * @return \Gateway\Entities\Customer
     */
    public function setAddressRequired()
    {
        $this->addressRequired = true;
        $this->required[0] = "billingAddress";
        $this->required[1] = "shippingAddress";
        return $this;
    }

    /**
     * Disable Shipping Address
     *
     * @return \Gateway\Entities\Customer
     */
    public function disableShipping()
    {
        if ($this->shippingAddress) {
            $this->shippingAddress->disable();
        }
        return $this;
    }

    /**
     * Set ID to be a required field
     *
     * @return \Gateway\Entities\Customer
     */
    public function setIdRequired()
    {
        $this->idRequired = true;
        $this->required[2] = "id";
        return $this;
    }

    /**
     * Set Date of birth of customer
     *
     * @param string $year
     * @param string $month
     * @param string $day
     * @return \Gateway\Entities\Customer
     */
    public function setDOB($year, $month, $day)
    {
        $this->year = $year;
        $this->month = $month;
        $this->day = $day;
        return $this;
    }

    /**
     * Set IP address
     *
     * @param string $ip
     * @return \Gateway\Entities\Customer
     */
    public function setIPAddress($ip)
    {
        $this->ipAddress = $ip;
        return $this;
    }
    /**
     * @inheritdoc
     */
    public function validate()
    {
        if($this->ipAddress !== null) {
            Validator::isIPAddress($this->ipAddress, true);
        }

        if (
            $this->year !== null 
            && $this->month !== null 
            && $this->day !== null
        ) {
            Validator::isDOB($this->year . '-' . $this->month . '-' . $this->day, true);
        }

        return (parent::validate()
            && Validator::isAlnumSpecial($this->getId(), 35, !$this->idRequired));
    }
}
