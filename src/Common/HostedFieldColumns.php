<?php
/**
 * Copyright 2023 Connfido BV. All rights reserved.
 * Distribution of this software without explicit written consent from Newgen is
 * strictly prohibited. No part of this software must not be reverse engineered,
 * copied, reproduced or modified.
 */

namespace Gateway\Common;

/**
 * NotificationChannel Constant objects
 *
 * @see Gateway\Common\ConstantObject
 * @final
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 */
final class HostedFieldColumns extends ConstantObject
{
    public static $CARD_NUMBER;

    public static $CARD_CVV;

    public static $CARD_HOLDER_NAME;

    public static $EXPIRY_DATE;

    /**
     * @inheritdoc
     */
    public static function init()
    {
        self::$CARD_NUMBER = new self('cardNumber');
        self::$CARD_CVV = new self('cardCvv');
        self::$CARD_HOLDER_NAME = new self('cardHolderName');
        self::$EXPIRY_DATE = new self('expiryDate');
    }
}

HostedFieldColumns::init();
