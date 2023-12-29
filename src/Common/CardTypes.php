<?php
/**
 * Copyright 2023 Connfido BV. All rights reserved.
 * Distribution of this software without explicit written consent from Newgen is
 * strictly prohibited. No part of this software must not be reverse engineered,
 * copied, reproduced or modified.
 */

namespace Gateway\Common;

/**
 * Card Types Constant objects
 *
 * @see Gateway\Common\AbstractType
 * @final
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 */
final class CardTypes extends AbstractType
{
    public static $AMERICANEXPRESS;

    public static $ASTROPAYCARD;

    public static $CARTASI;

    public static $CARTEBLEUE;

    public static $CMRFALABELLA;

    public static $CORDIAL;

    public static $DANKORT;

    public static $DINERSCLUB;

    public static $DISCOVER;

    public static $HIPERCARD;

    public static $JCB;

    public static $LASER;

    public static $MASTER_CARD;

    public static $MASTER;

    public static $MAESTRO;

    public static $MAESTROUK;

    public static $POSTEPAY;

    public static $PRESTO;

    public static $RUPAY;

    public static $SOLO;

    public static $UNIONPAY;

    public static $VISA_CARD;

    public static $VISA;

    public static $VISAELECTRON;

    public static $VPAY;

    /**
     * @inheritdoc
     */
    public static function init()
    {
        self::$AMERICANEXPRESS = new self("AmericanExpress");
        self::$ASTROPAYCARD = new self("AstropayCard");
        self::$CARTASI = new self("CARTASI");
        self::$CARTEBLEUE = new self("CARTEBLEUE");
        self::$CMRFALABELLA = new self("CMRFalabella");
        self::$CORDIAL = new self("Cordial");
        self::$DANKORT = new self("DANKORT");
        self::$DINERSCLUB = new self("DinersClub");
        self::$DISCOVER = new self("DISCOVER");
        self::$HIPERCARD = new self("HIPERCARD");
        self::$JCB = new self("JCB");
        self::$LASER = new self("LASER");
        self::$MASTER_CARD = new self("MasterCard");
        self::$MASTER = self::$MASTER_CARD;
        self::$MAESTRO = new self("Maestro");
        self::$MAESTROUK = new self("MaestroUK");
        self::$POSTEPAY = new self("POSTEPAY");
        self::$PRESTO = new self("Presto");
        self::$RUPAY = new self("Rupay");
        self::$SOLO = new self("SOLO");
        self::$UNIONPAY = new self("UNIONPAY");
        self::$VISA_CARD = new self("VisaCard");
        self::$VISA = self::$VISA_CARD;
        self::$VISAELECTRON = new self("VISAELECTRON");
        self::$VPAY = new self("VPAY");
    }
}

CardTypes::init();
