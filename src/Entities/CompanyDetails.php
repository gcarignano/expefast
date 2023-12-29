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
use Gateway\Common\Currency;

/**
 * Company Details
 *
 * @see Gateway\Entities\AbstractEntity
 * @final
 */
final class CompanyDetails extends AbstractEntity
{
    /**
     * Country
     *
     * @var string
     */
    protected $country;

    /**
     * Transaction Amount
     *
     * @var string
     */
    protected $amount;

    /**
     * Transaction Currency Code
     *
     * @var string
     */
    protected $currencyCode;

    /**
     * @param string $registrationID
     * @param string $country
     * @param string $currencyCode
     * @param string $amount
     */
    public function __construct($registrationID, $country, $currencyCode, $amount = null)
    {
        parent::__construct($registrationID);
        $this->country = $country;
        $this->amount = $amount;
        $this->currencyCode = $currencyCode;
    }

    /**
     * @inheritdoc
     */
    protected $required = [
        "id", "country", "currencyCode"
    ];

    /**
     * @inheritdoc
     */
    public function getArray()
    {
        $data = [
            Fields::COMPANY_ID => $this->getId(),
            Fields::COUNTRY => $this->country,
            Fields::CURRENCYCODE => $this->currencyCode->getValue(),
        ];
        if (! empty($this->amount)) {
            $data[Fields::COMPANY_AMOUNT] = $this->amount;
        }
        return $data;
    }

    /**
     * @inheritdoc
     */
    public function validate()
    {
        return (
            parent::validate()
            && Validator::isAmount($this->amount, false, true)
            && Validator::isAllSpecial($this->country, 100)
            && Validator::isAlnumSpecial($this->getId(), 50)
            && ($this->currencyCode instanceof Currency)
        );
    }
}
