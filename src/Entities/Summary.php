<?php
/**
 * Copyright 2023 Connfido BV. All rights reserved.
 * Distribution of this software without explicit written consent from Newgen is
 * strictly prohibited. No part of this software must not be reverse engineered,
 * copied, reproduced or modified.
 */

namespace Gateway\Entities;

use Gateway\Utility\Validator;
use Gateway\Exceptions\ValidationException;
use Gateway\Common\Fields;
use Gateway\Common\Messages;

/**
 * Order Summary
 *
 * @see Gateway\Entities\AbstractEntity
 * @final
 */
final class Summary extends AbstractEntity
{
    /**
     * @var array
     */
    protected $data = [];

    /**
     * @inheritdoc
     */
    protected $required = [
        "data"
    ];

    /**
     * @param string $subtotal
     * @param string $tax
     * @param string $shippingCharges
     */
    public function __construct($subtotal, $tax, $shippingCharges)
    {
        $this->addDetails($subtotal, $tax, $shippingCharges);
    }

    /**
     * @inheritdoc
     */
    public function getArray()
    {
        return $this->data;
    }

    /**
     * @inheritdoc
     */
    public function validate()
    {
        if (isset($this->data[Fields::DETAILS])) {
            try {
                Validator::isAmount($this->data[Fields::DETAILS][Fields::SUBTOTAL]);
                Validator::isAmount($this->data[Fields::DETAILS][Fields::TAX]);
                Validator::isAmount($this->data[Fields::DETAILS][Fields::SHIPPINGPRICE]);
            } catch (ValidationException $e) {
                throw new ValidationException(Messages::SUMMARY_DETAIL);
            }
        }
        if (isset($this->data[Fields::DISCOUNT])) {
            try {
                Validator::isAmount($this->data[Fields::DISCOUNT][Fields::DISCOUNT_AMOUNT]);
                Validator::isAlnumSpecial($this->data[Fields::DISCOUNT][Fields::COUPON_CODE], 255);
                Validator::isAllSpecial($this->data[Fields::DISCOUNT][Fields::COUPON_DESC]);
            } catch (ValidationException $e) {
                throw new ValidationException(Messages::SUMMARY_DISCOUNT);
            }
        }
        return true;
    }

    /**
     * Adds Order details
     *
     * @param string $subtotal
     * @param string $tax
     * @param string $shippingCharges
     * @return \Gateway\Entities\Summary
     */
    private function addDetails($subtotal, $tax, $shippingCharges)
    {
        $this->data[Fields::DETAILS] = [
            Fields::SUBTOTAL => $subtotal,
            Fields::TAX => $tax,
            Fields::SHIPPINGPRICE => $shippingCharges
        ];
        return $this;
    }

    /**
     * Adds Order discount details
     *
     * @param string $amount
     * @param string $code
     * @param string $detail
     * @return \Gateway\Entities\Summary
     */
    public function addDiscount($amount, $code, $detail)
    {
        $this->data[Fields::DISCOUNT] = [
            Fields::DISCOUNT_AMOUNT => $amount,
            Fields::COUPON_CODE => $code,
            Fields::COUPON_DESC => $detail
        ];
        return $this;
    }
}
