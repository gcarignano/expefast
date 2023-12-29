<?php
/**
 * Copyright 2023 Connfido BV. All rights reserved.
 * Distribution of this software without explicit written consent from Newgen is
 * strictly prohibited. No part of this software must not be reverse engineered,
 * copied, reproduced or modified.
 */

namespace Gateway\Common;

/**
 * Constant Objects
 *
 * @abstract
 */
abstract class ConstantObject
{
    /**
     * Value of a constant object
     *
     * @var mixed
     */
    protected $value;

    /**
     * @param mixed $constantValue
     */
    protected function __construct($constantValue)
    {
        $this->value = $constantValue;
    }

    /**
     * Gets value of a constant object
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Initialize constant objects
     * @abstract
     */
    abstract public static function init();
}
