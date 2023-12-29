<?php
/**
 * Copyright 2023 Connfido BV. All rights reserved.
 * Distribution of this software without explicit written consent from Newgen is
 * strictly prohibited. No part of this software must not be reverse engineered,
 * copied, reproduced or modified.
 */

namespace Gateway\Entities;

use Gateway\Common\Fields;
use Gateway\Common\Messages;
use Gateway\Exceptions\ValidationException;
use Gateway\Utility\Validator;

/**
 * WireTransfer
 *
 * @see Gateway\Entities\AbstractWireTransferBank
 */
final class WireTransferBankEEA extends AbstractWireTransferBank
{
    /**
     * @var string
     */
    protected $iban;

    /**
     * @var string
     */
    protected $bic;

    /**
     * @inheritdoc
     */
    protected $required = [
        'iban', 'bic'
    ];

    /**
     * @param string $name
     * @param string $address
     * @param string $bic
     * @param string $iban
     */
    public function __construct($name, $address, $bic, $iban)
    {
        parent::__construct($name, $address, $bic);
        $this->iban = $iban;
    }

    /**
     * @inheritDoc
     */
    public function getArray()
    {
        return array_merge(
            parent::getArray(),
            [
                Fields::IBAN => $this->iban
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function validate()
    {
        try {
            Validator::isAlnumSpecial($this->iban, 32, true);
        } catch (ValidationException $e) {
            throw new ValidationException(Messages::IBAN, null, 0, $e);
        }
        return parent::validate();
    }

    /**
     * @inheritDoc
     */
    public function getEEA()
    {
        return true;
    }
}
