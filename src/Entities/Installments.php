<?php
/**
 * Copyright 2023 Connfido BV. All rights reserved.
 * Distribution of this software without explicit written consent from Newgen is
 * strictly prohibited. No part of this software must not be reverse engineered,
 * copied, reproduced or modified.
 */

namespace Gateway\Entities;

use Gateway\Common\Fields;
use Gateway\Common\Currency;
use Gateway\Common\InstallmentPeriod;
use Gateway\Common\InstallmentType;
use Gateway\Utility\Validator;

/**
 * Subscription Installments
 *
 * @see Gateway\Entities\AbstractEntity
 * @final
 */
final class Installments extends AbstractEntity
{
    /**
     * @var mixed
     */
    protected $installments = [];

    /**
     * @param Currency $currency
     * @param string|float $amount
     * @param InstallmentPeriod $period
     * @param string|int $frequency
     * @param string|int $totalInstallments
     * @param string|int $sequence
     * @param InstallmentType $type
     */
    public function __construct(
        Currency $currency,
        $amount,
        InstallmentPeriod $period,
        $frequency,
        $totalInstallments,
        $sequence,
        InstallmentType $type
    ) {
        $this->addInstallment($currency, $amount, $period, $frequency, $totalInstallments, $sequence, $type);
    }

    /**
     * @inheritdoc
     */
    public function getArray()
    {
        return $this->installments;
    }

    /**
     * @inheritdoc
     */
    public function validate()
    {
        foreach ($this->installments as $installment) {
            Validator::isAmount($installment[Fields::SUB_AMOUNT]);
            Validator::isNum($installment[Fields::SUB_FREQ], 2);
            Validator::isNum($installment[Fields::SUB_INSTALLMENTS_TOTAL], 2);
            Validator::isNum($installment[Fields::SUB_SEQUENCE], 2);
        }
        return parent::validate();
    }

    /**
     * Add subscription installment
     *
     * @return \Gateway\Entities\Installments
     */
    public function addInstallment(
        Currency $currency,
        $amount,
        InstallmentPeriod $period,
        $frequency,
        $totalInstallments,
        $sequence,
        InstallmentType $type
    ) {
        $this->installments[] = [
            Fields::CURRENCYCODE => $currency->getValue(),
            Fields::SUB_AMOUNT => $amount,
            Fields::SUB_PERIOD => $period->getValue(),
            Fields::SUB_FREQ => $frequency,
            Fields::SUB_INSTALLMENTS_TOTAL => $totalInstallments,
            Fields::SUB_SEQUENCE => $sequence,
            Fields::SUB_TYPE => $type->getValue()
        ];
        return $this;
    }
}
