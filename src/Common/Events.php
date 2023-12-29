<?php
/**
 * Copyright 2023 Connfido BV. All rights reserved.
 * Distribution of this software without explicit written consent from Newgen is
 * strictly prohibited. No part of this software must not be reverse engineered,
 * copied, reproduced or modified.
 */

namespace Gateway\Common;

/**
 * Webhook event types
 *
 * @see Gateway\Common\ConstantObject
 * @final
 */
final class Events extends ConstantObject
{
    public static $all;

    public static $capture;

    public static $captureFailed;

    public static $chargeback;

    public static $expired;

    public static $payment;

    public static $payouts;

    public static $refund;

    public static $void;

    public static $voidFailed;

    /**
     * @inheritdoc
     */
    public static function init()
    {
        self::$all = new self("*");
        self::$capture = new self("capture");
        self::$captureFailed = new self("captureFailed");
        self::$chargeback = new self("chargeback");
        self::$expired = new self("expired");
        self::$payment = new self("payment");
        self::$payouts = new self("payouts");
        self::$refund = new self("refund");
        self::$void = new self("void");
        self::$voidFailed = new self("voidFailed");
    }
}

Events::init();
