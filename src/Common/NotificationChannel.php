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
final class NotificationChannel extends ConstantObject
{
    public static $EMAIL;

    public static $SLACK;

    public static $SMS;

    public static $WHATSAPP;

    /**
     * @inheritdoc
     */
    public static function init()
    {
        self::$EMAIL = new self('email');
        self::$SLACK = new self('slack');
        self::$SMS = new self('sms');
        self::$WHATSAPP = new self('whatsapp');
    }
}

NotificationChannel::init();
