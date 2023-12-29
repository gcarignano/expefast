<?php
/**
 * Copyright 2023 Connfido BV. All rights reserved.
 * Distribution of this software without explicit written consent from Newgen is
 * strictly prohibited. No part of this software must not be reverse engineered,
 * copied, reproduced or modified.
 */

namespace Gateway\Common;

/**
 * Installment Period
 *
 * @see Gateway\Common\ConstantObject
 * @final
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 */
final class InstallmentPeriod extends ConstantObject
{
    public static $DAY;

    public static $WEEK;

    public static $MONTH;

    public static $YEAR;

    /**
     * @inheritdoc
     */
    public static function init()
    {
        self::$DAY = new self('DAY');
        self::$WEEK = new self('WEEK');
        self::$MONTH = new self('MONTH');
        self::$YEAR = new self('YEAR');
    }
}

InstallmentPeriod::init();
