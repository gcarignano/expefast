<?php
/**
 * Copyright 2023 Connfido BV. All rights reserved.
 * Distribution of this software without explicit written consent from Newgen is
 * strictly prohibited. No part of this software must not be reverse engineered,
 * copied, reproduced or modified.
 */

namespace Gateway\Common;

/**
 * PaymentMode
 *
 * @see Gateway\Common\ConstantObject
 * @final
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
final class PaymentMode extends ConstantObject
{
    public static $APPLE_PAY;

    public static $ASTROPAY;

    public static $ASTROPAYCARD;

    public static $BANAMEX;

    public static $BCMC;

    public static $BOKU;

    public static $BOLETO;

    public static $CHINAUNIONPAY;

    public static $CREDITCARD;

    public static $DEBITCARD;

    public static $EPS;

    public static $GIROPAY;

    public static $GOOGLEPAY;

    public static $IDEAL;

    public static $INTERAC;

    public static $INTERAC_TRANSFER;

    public static $INVOICEME;

    public static $KLARNA;

    public static $KLARNA_INVOICE;

    public static $MERCADOLIVRE;

    public static $MONETA;

    public static $MUCH_BETTER;

    public static $MULTIBANCO;

    public static $NETELLER;

    public static $OXXO;

    public static $PAYPAL;

    public static $PAYSAFECARD;

    public static $PICPAY;

    public static $PIX;

    public static $PNP;

    public static $SEPA;

    public static $SKRILL;

    public static $SOFORT;

    public static $SOLO;

    public static $TED;

    public static $TRUSTLY;

    public static $TRUSTLYDD;

    public static $VIP_PASS;

    public static $WIRE_TRANSFER;

    public static $RAPID_TRANSFER;

    public static $UPI;

    public static $SWISH;

    /**
     * @var boolean
     */
    private $isCard;

    /**
     * @var boolean
     */
    private $allowsType;

    /**
     * @var boolean
     */
    private $whppRequireData;

    /**
     * @var boolean
     */
    private $payout;

    /**
     * @var boolean
     */
    private $additionalData;

    /**
     * @param string $mode
     * @param boolean $isCard
     * @param boolean $allowsType
     * @param boolean $whppRequireData
     * @param boolean $payout
     */
    protected function __construct(
        $mode,
        $isCard = false,
        $allowsType = false,
        $whppRequireData = false,
        $payout = false,
        $additionalData = false
    ) {
        parent::__construct($mode);
        $this->isCard = $isCard;
        $this->allowsType = $allowsType;
        $this->whppRequireData = $whppRequireData;
        $this->payout = $payout;
        $this->additionalData = $additionalData;
    }

    /**
     * @inheritdoc
     */
    public static function init()
    {
        self::$APPLE_PAY = new self("APPLE_PAY", false, false, false, false, true);
        self::$ASTROPAY = new self("ASTROPAY");
        self::$ASTROPAYCARD = new self("ASTROPAYCARD");
        self::$BANAMEX = new self("BANAMEX");
        self::$BCMC = new self("BCMC");
        self::$BOKU = new self("BOKU");
        self::$BOLETO = new self("BOLETO");
        self::$CHINAUNIONPAY = new self("CHINAUNIONPAY");
        self::$CREDITCARD = new self("CreditCard", true, true, true, true);
        self::$DEBITCARD = new self("DebitCard", true, true, true);
        self::$EPS = new self("EPS");
        self::$GIROPAY = new self("GIROPAY", false, false, true);
        self::$GOOGLEPAY = new self("GOOGLE_PAY", true, false, false, false, true);
        self::$IDEAL = new self("IDEAL", false, true, false);
        self::$INTERAC = new self("INTERAC_ONLINE", false, false, false, true);
        self::$INTERAC_TRANSFER = new self("INTERAC_TRANSFER", false, false, false, true);
        self::$INVOICEME = new self("INVOICEME");
        self::$KLARNA = new self("KLARNA_INSTALLMENTS");
        self::$KLARNA_INVOICE = new self("KLARNA_INVOICE");
        self::$MERCADOLIVRE = new self("MERCADOLIVRE");
        self::$MONETA = new self("MONETA");
        self::$MUCH_BETTER = new self("MUCH_BETTER");
        self::$MULTIBANCO = new self("MULTIBANCO");
        self::$NETELLER = new self("NETELLER", false, false, false, true);
        self::$OXXO = new self("OXXO");
        self::$PAYPAL = new self("PAYPAL");
        self::$PAYSAFECARD = new self("PAYSAFECARD");
        self::$PICPAY = new self("PICPAY", false, false, false, false, true);
        self::$PIX = new self("PIX", false, false, false, false, true);
        self::$PNP = new self("PNP");
        self::$SEPA = new self("DIRECTDEBIT_SEPA", false, false, true);
        self::$SKRILL = new self("SKRILL_WALLET", false, false, false, true);
        self::$SOFORT = new self("SOFORTUEBERWEISUNG");
        self::$SOLO = new self("SOLO");
        self::$TED = new self("TED");
        self::$TRUSTLY = new self("TRUSTLY_DEPOSIT");
        self::$VIP_PASS = new self("VIP_PASS");
        self::$TRUSTLYDD = new self("TRUSTLY_DIRECT_DEBIT", false, false, false, true);
        self::$WIRE_TRANSFER = new self("WIRE_TRANSFER", false, false, false, false, true);
        self::$RAPID_TRANSFER = new self("RAPID_TRANSFER", false, false, false, true);
        self::$UPI = new self("UPI", false, false, false, true);
        self::$SWISH = new self("SWISH", false, false, false, true);
    }

    /**
     * Checks if Payment Mode is a Card Type payment
     *
     * @return boolean
     */
    public function isCardMode()
    {
        return $this->isCard;
    }

    /**
     * Checks if Payment Mode supports Payment Types
     *
     * @return boolean
     */
    public function canHaveType()
    {
        return $this->allowsType;
    }

    /**
     * Checks if Payment Mode requires extra information for WHPP
     *
     * @return boolean
     */
    public function whppExtraInfo()
    {
        return $this->whppRequireData;
    }

    public function allowsAdditionalData()
    {
        return $this->additionalData;
    }

    public function isPayoutMode()
    {
        return $this->payout;
    }
}

PaymentMode::init();
