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
 * Bank Information for WHPP
 *
 * @see Gateway\Entities\WhppInfoInterface
 * @see Gateway\Entities\AbstractEntity
 * @final
 */
final class BankInfo extends AbstractEntity implements WhppInfoInterface
{
    /**
     * Bank Identification Code
     *
     * @var string
     */
    private $asBIC = true;

    /**
     * Card holder name
     *
     * @var string
     */
    private $holderName;

    /**
     * @inheritdoc
     */
    protected $required = [
        "id"
    ];

    /**
     * @param string $number
     * @param string $name
     */
    public function __construct($number, $name = null)
    {
        parent::__construct($number);
        if (! empty($name)) {
            $this->holderName = $name;
            $this->asBIC = false;
        }
    }

    /**
     * @inheritdoc
     */
    public function getArray()
    {
        $data = [
            Fields::IBAN => $this->getId(),
            Fields::BANK_HOLDER => $this->holderName
        ];
        if ($this->asBIC) {
            $data = [
                Fields::BIC => $this->getId()
            ];
        }
        return $data;
    }

    /**
     * @inheritdoc
     */
    public function validate()
    {
        if ($this->asBIC) {
            try {
                Validator::isAlnumSpecial($this->getId(), 11, true);
            } catch (ValidationException $e) {
                throw new ValidationException(Messages::BIC, null, 0, $e);
            }
        } else {
            try {
                Validator::isAlnumSpecial($this->holderName, 150, true);
            } catch (ValidationException $e) {
                throw new ValidationException(Messages::AC_HOLDER, null, 0, $e);
            }
            try {
                Validator::isAlnumSpecial($this->getId(), 31, true);
            } catch (ValidationException $e) {
                throw new ValidationException(Messages::IBAN, null, 0, $e);
            }
        }
        return parent::validate();
    }
}
