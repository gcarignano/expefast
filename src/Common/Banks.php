<?php
/**
 * Copyright 2023 Connfido BV. All rights reserved.
 * Distribution of this software without explicit written consent from Newgen is
 * strictly prohibited. No part of this software must not be reverse engineered,
 * copied, reproduced or modified.
 */

namespace Gateway\Common;

/**
 * Banks Constant objects
 *
 * @see Gateway\Common\AbstractType
 * @final
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 * @SuppressWarnings(PHPMD.LongVariable)
 */
final class Banks extends AbstractType
{
    public static $ABN_AMRO;

    public static $ASNBANK;

    public static $ING;

    public static $BUNQ_BANK;

    public static $KNAB;

    public static $INGBNL2A;

    public static $SNSBANK;

    public static $RABOBANK;

    public static $REGIOBANK;

    public static $VAN_LANSCHOT_BANKIERS;

    public static $TRIODOSBANK;

    /**
     * @inheritdoc
     */
    public static function init()
    {
        self::$ABN_AMRO = new self('ABNANL2A');
        self::$ASNBANK = new self('ASN Bank');
        self::$ING = new self('ING');
        self::$BUNQ_BANK = new self('BUNQ_BANK');
        self::$KNAB = new self('knab');
        self::$INGBNL2A = new self('INGBNL2A');
        self::$SNSBANK = new self('SNS Bank');
        self::$RABOBANK = new self('Rabobank');
        self::$REGIOBANK = new self('RegioBank');
        self::$VAN_LANSCHOT_BANKIERS = new self('VAN_LANSCHOT_BANKIERS');
        self::$TRIODOSBANK = new self('Triodos Bank');
    }
}

Banks::init();
