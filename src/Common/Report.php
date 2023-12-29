<?php
/**
 * Copyright 2023 Connfido BV. All rights reserved.
 * Distribution of this software without explicit written consent from Newgen is
 * strictly prohibited. No part of this software must not be reverse engineered,
 * copied, reproduced or modified.
 */

namespace Gateway\Common;

/**
 * Report Type Constant objects
 *
 * @see Gateway\Common\ConstantObject
 * @final
 */
final class Report extends ConstantObject
{
    public static $TRANSACTION;

    public static $REFUND;

    public static $CHARGEBACK;

    public static $PLANS;

    public static $SUBSCRIPTION_TRANSACTION;

    public static $SUBSCRIPTIONS;

    /**
     * @inheritdoc
     */
    public static function init()
    {
        self::$TRANSACTION = new self('transactionReport');
        self::$REFUND = new self('refundReport');
        self::$CHARGEBACK = new self('chargebackReport');
        self::$PLANS = new self('plansReport');
        self::$SUBSCRIPTION_TRANSACTION = new self('subscriptionTransactionsReport');
        self::$SUBSCRIPTIONS = new self('subscriptionsReport');
    }
}

Report::init();
