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
 * Dynamic Desciptor
 *
 * @see Gateway\Entities\AbstractEntity
 * @final
 */
final class DynamicDescriptor extends AbstractEntity
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var string
     */
    protected $mobile;

    /**
     * @param string $name
     * @param string $email
     * @param string $mobile
     */
    public function __construct(
        $mobile = null,
        $email = null,
        $name = null
    ) {
        $this->mobile = trim($mobile);
        $this->email = trim($email);
        $this->name = trim($name);
    }

    /**
     * @inheritdoc
     */
    public function getArray()
    {
        $data = [];
        if ($this->mobile) {
            $data[Fields::DD_MOBILE] = $this->mobile;
        }
        if ($this->email) {
            $data[Fields::DD_EMAIL] = $this->email;
        }
        if ($this->name) {
            $data[Fields::DD_NAME] = $this->name;
        }
        return empty($data) ? $data : [Fields::DD => $data];
    }

    /**
     * @inheritdoc
     */
    public function validate()
    {
        return (parent::validate()
            && Validator::isUniAlnumSpecial($this->name, 255, true)
            && Validator::isEmail($this->email, true)
        );
    }
}
