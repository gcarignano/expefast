<?php
/**
 * Copyright 2023 Connfido BV. All rights reserved.
 * Distribution of this software without explicit written consent from Newgen is
 * strictly prohibited. No part of this software must not be reverse engineered,
 * copied, reproduced or modified.
 */

namespace Gateway\Common;

/**
 * Report Sort Order Constant objects
 *
 * @see Gateway\Common\ConstantObject
 * @final
 */
final class SortOrder extends ConstantObject
{
    public static $ASC;

    public static $DESC;

    /**
     * @inheritdoc
     */
    public static function init()
    {
        self::$ASC = new self("asc");
        self::$DESC = new self("desc");
    }
}

SortOrder::init();
