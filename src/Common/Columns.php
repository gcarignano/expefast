<?php
/**
 * Copyright 2023 Connfido BV. All rights reserved.
 * Distribution of this software without explicit written consent from Newgen is
 * strictly prohibited. No part of this software must not be reverse engineered,
 * copied, reproduced or modified.
 */

namespace Gateway\Common;

/**
 * Report Columns Constant objects
 *
 * @see Gateway\Common\ConstantObject
 * @final
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 */
final class Columns extends ConstantObject
{
    public static $TXN_DATE;

    public static $TXN_REFERENCE;

    public static $PAYMENT_MODE;

    public static $STATUS;

    public static $CURRENCY;

    public static $CARDHOLDER_NAME;

    public static $IP_ADDRESS;

    public static $CUSTOMER_ID;

    public static $SUBSCRIPTION_ID;

    /**
     * @inheritdoc
     */
    public static function init()
    {
        self::$TXN_DATE = new self('transactionDate');
        self::$TXN_REFERENCE = new self('txnReference');
        self::$PAYMENT_MODE = new self('paymentMode');
        self::$STATUS = new self('status');
        self::$CURRENCY = new self('currencyCode');
        self::$CARDHOLDER_NAME = new self('cardHolderName');
        self::$IP_ADDRESS = new self('ipAddress');
        self::$CUSTOMER_ID = new self('customerId');
        self::$SUBSCRIPTION_ID = new self('subscriptionId');
    }
}

Columns::init();
