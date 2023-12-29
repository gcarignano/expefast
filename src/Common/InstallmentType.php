<?php
/**
 * Copyright 2023 Connfido BV. All rights reserved.
 * Distribution of this software without explicit written consent from Newgen is
 * strictly prohibited. No part of this software must not be reverse engineered,
 * copied, reproduced or modified.
 */

namespace Gateway\Common;

/**
 * Installment Type
 *
 * @see Gateway\Common\ConstantObject
 * @final
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 */
final class InstallmentType extends ConstantObject
{
    public static $REGULAR;

    public static $TRIAL;

    /**
     * @inheritdoc
     */
    public static function init()
    {
        self::$REGULAR = new self('REGULAR');
        self::$TRIAL = new self('TRIAL');
    }
}

InstallmentType::init();
