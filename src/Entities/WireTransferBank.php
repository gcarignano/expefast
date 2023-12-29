<?php
/**
 * Copyright 2023 Connfido BV. All rights reserved.
 * Distribution of this software without explicit written consent from Newgen is
 * strictly prohibited. No part of this software must not be reverse engineered,
 * copied, reproduced or modified.
 */

namespace Gateway\Entities;

use Gateway\Common\Fields;
use Gateway\Utility\Validator;

/**
 * WireTransfer
 *
 * @see Gateway\Entities\AbstractWireTransferBank
 */
final class WireTransferBank extends AbstractWireTransferBank
{
    /**
     * @var string
     */
    protected $accountNumber;

    /**
     * @var string
     */
    protected $sortCode;

    /**
     * @inheritdoc
     */
    protected $required = [
        'accountNumber', 'sortCode'
    ];

    /**
     * @param string $accountNumber
     * @param string $sortCode
     * @param string $iban
     * @param string $bic
     */
    public function __construct(
        $name,
        $address,
        $bic,
        $accountNumber,
        $sortCode
    ) {
        parent::__construct($name, $address, $bic);
        $this->accountNumber = $accountNumber;
        $this->sortCode = $sortCode;
    }


    /**
     * @inheritdoc
     */
    public function getArray()
    {
        return array_merge(
            parent::getArray(),
            [
                Fields::WIRETRANSFER_BANK_ACCOUNT_NUMBER => $this->accountNumber,
                Fields::WIRETRANSFER_BANK_SORT_CODE => $this->sortCode
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function validate()
    {
        return parent::validate()
            && Validator::isNum($this->accountNumber, 30)
            && Validator::isNum($this->sortCode, 10);
    }

    /**
     * @inheritDoc
     */
    public function getEEA()
    {
        return false;
    }
}
