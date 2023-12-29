<?php
/**
 * Copyright 2023 Connfido BV. All rights reserved.
 * Distribution of this software without explicit written consent from Newgen is
 * strictly prohibited. No part of this software must not be reverse engineered,
 * copied, reproduced or modified.
 */

namespace Gateway\Entities;

use Gateway\Utility\Validator;
use Gateway\Common\Currency;
use Gateway\Common\Fields;

/**
 * Transaction
 *
 * @see Gateway\Entities\AbstractEntity
 * @final
 */
final class Transaction extends AbstractEntity
{
    /**
     * Transaction amount
     *
     * @var string
     */
    protected $amount;

    /**
     * Currency Code
     *
     * @var \Gateway\Common\Currency
     */
    protected $currency;

    /**
     * @var boolean
     */
    protected $isApp = false;

    /**
     * @var boolean
     */
    protected $zeroAuth = false;

    /**
     * @inheritdoc
     */
    protected $required = [
        "id", "currency", "amount"
    ];

    /**
     * @param string $txnId
     * @param string $txnAmount
     * @param \Gateway\Common\Currency $currencyCode
     */
    public function __construct($txnId, $txnAmount, Currency $currencyCode)
    {
        parent::__construct($txnId);
        $this->currency = $currencyCode;
        $this->amount = (float)$txnAmount;
    }

    /**
     * @inheritdoc
     */
    public function getArray()
    {
        return [
            Fields::TXN_REFERENCE => $this->getId(),
            Fields::TXN_AMOUNT => $this->amount,
            Fields::CURRENCYCODE => $this->currency->getValue(),
            Fields::IS_APP => $this->isApp,
        ];
    }

    /**
     * @inheritdoc
     */
    public function validate()
    {
        return (
            parent::validate()
            && Validator::isAmount($this->amount, $this->zeroAuth)
            && Validator::isAlnumSpecial($this->getId(), 100)
            && ($this->currency instanceof Currency)
        );
    }

    /**
     * @return \Gateway\Entities\Transaction
     */
    public function asApp()
    {
        $this->isApp = true;
        return $this;
    }

    /**
     * @return \Gateway\Entities\Transaction
     */
    public function setZeroAuth()
    {
        $this->zeroAuth = true;
        return $this;
    }
}
