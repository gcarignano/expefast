<?php
/**
 * Copyright 2023 Connfido BV. All rights reserved.
 * Distribution of this software without explicit written consent from Newgen is
 * strictly prohibited. No part of this software must not be reverse engineered,
 * copied, reproduced or modified.
 */

namespace Gateway\Entities;

use Gateway\Entities\AbstractEntity;
use Gateway\Common\Fields;
use Gateway\Utility\Validator;

abstract class AbstractWireTransferBank extends AbstractEntity
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $address;

    /**
     * @var string
     */
    protected $bic;

    /**
     * @inheritdoc
     */
    protected $required = [
        'name', 'address', 'bic'
    ];

    /**
     * @param string $address
     * @param string $name
     */
    public function __construct(
        $address,
        $name,
        $bic
    ) {
        $this->address = $address;
        $this->name = $name;
        $this->bic = $bic;
    }

    /**
     * @inheritdoc
     */
    public function getArray()
    {
        return [
            Fields::WIRETRANSFER_BANK_ADDRESS => $this->address,
            Fields::WIRETRANSFER_BANK_NAME => $this->name,
            Fields::BIC => $this->bic
        ];
    }

    /**
     * @inheritdoc
     */
    public function validate()
    {
        return parent::validate()
        && Validator::isAlnumSpecial($this->address, 250)
        && Validator::isAlnumSpecial($this->name, 250)
        && Validator::isAlnumSpecial($this->bic, 11);
    }

    /**
     * @return bool
     */
    abstract public function getEEA();
}
