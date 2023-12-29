<?php
/**
 * Copyright 2023 Connfido BV. All rights reserved.
 * Distribution of this software without explicit written consent from Newgen is
 * strictly prohibited. No part of this software must not be reverse engineered,
 * copied, reproduced or modified.
 */

namespace Gateway\Common;

/**
 * Currency Code Constant objects
 *
 * @see Gateway\Common\ConstantObject
 * @final
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
final class Currency extends ConstantObject
{
    public static $AFN;

    public static $EUR;

    public static $ALL;

    public static $DZD;

    public static $USD;

    public static $AOA;

    public static $XCD;

    public static $ARS;

    public static $AMD;

    public static $AWG;

    public static $AUD;

    public static $AZN;

    public static $BSD;

    public static $BHD;

    public static $BDT;

    public static $BBD;

    public static $BYN;

    public static $BZD;

    public static $XOF;

    public static $BMD;

    public static $INR;

    public static $BTN;

    public static $BOB;

    public static $BOV;

    public static $BAM;

    public static $BWP;

    public static $NOK;

    public static $BRL;

    public static $BND;

    public static $BGN;

    public static $BIF;

    public static $CVE;

    public static $KHR;

    public static $XAF;

    public static $CAD;

    public static $KYD;

    public static $CLP;

    public static $CLF;

    public static $CNY;

    public static $COP;

    public static $COU;

    public static $KMF;

    public static $CDF;

    public static $NZD;

    public static $CRC;

    public static $HRK;

    public static $CUP;

    public static $CUC;

    public static $ANG;

    public static $CZK;

    public static $DKK;

    public static $DJF;

    public static $DOP;

    public static $EGP;

    public static $SVC;

    public static $ERN;

    public static $ETB;

    public static $FKP;

    public static $FJD;

    public static $XPF;

    public static $GMD;

    public static $GEL;

    public static $GHS;

    public static $GIP;

    public static $GTQ;

    public static $GBP;

    public static $GNF;

    public static $GYD;

    public static $HTG;

    public static $HNL;

    public static $HKD;

    public static $HUF;

    public static $ISK;

    public static $IDR;

    public static $XDR;

    public static $IRR;

    public static $IQD;

    public static $ILS;

    public static $JMD;

    public static $JPY;

    public static $JOD;

    public static $KZT;

    public static $KES;

    public static $KPW;

    public static $KRW;

    public static $KWD;

    public static $KGS;

    public static $LAK;

    public static $LBP;

    public static $LSL;

    public static $ZAR;

    public static $LRD;

    public static $LYD;

    public static $CHF;

    public static $MOP;

    public static $MKD;

    public static $MGA;

    public static $MWK;

    public static $MYR;

    public static $MVR;

    public static $MRU;

    public static $MUR;

    public static $XUA;

    public static $MXN;

    public static $MXV;

    public static $MDL;

    public static $MNT;

    public static $MAD;

    public static $MZN;

    public static $MMK;

    public static $NAD;

    public static $NPR;

    public static $NIO;

    public static $NGN;

    public static $OMR;

    public static $PKR;

    public static $PAB;

    public static $PGK;

    public static $PYG;

    public static $PEN;

    public static $PHP;

    public static $PLN;

    public static $QAR;

    public static $RON;

    public static $RUB;

    public static $RWF;

    public static $SHP;

    public static $WST;

    public static $STN;

    public static $SAR;

    public static $RSD;

    public static $SCR;

    public static $SLL;

    public static $SGD;

    public static $XSU;

    public static $SBD;

    public static $SOS;

    public static $SSP;

    public static $LKR;

    public static $SDG;

    public static $SRD;

    public static $SZL;

    public static $SEK;

    public static $CHE;

    public static $CHW;

    public static $SYP;

    public static $TWD;

    public static $TJS;

    public static $TZS;

    public static $THB;

    public static $TOP;

    public static $TTD;

    public static $TND;

    public static $TRY;

    public static $TMT;

    public static $UGX;

    public static $UAH;

    public static $AED;

    public static $USN;

    public static $UYU;

    public static $UYI;

    public static $UYW;

    public static $UZS;

    public static $VUV;

    public static $VES;

    public static $VND;

    public static $YER;

    public static $ZMW;

    public static $ZWL;

    public static $XBA;

    public static $XBB;

    public static $XBC;

    public static $XBD;

    public static $XTS;

    public static $XXX;

    public static $XAU;

    public static $XPD;

    public static $XPT;

    public static $XAG;
    /**
     * @inheritdoc
     */
    public static function init()
    {
        self::$AFN = new self("AFN");
        self::$EUR = new self("EUR");
        self::$ALL = new self("ALL");
        self::$DZD = new self("DZD");
        self::$USD = new self("USD");
        self::$AOA = new self("AOA");
        self::$XCD = new self("XCD");
        self::$ARS = new self("ARS");
        self::$AMD = new self("AMD");
        self::$AWG = new self("AWG");
        self::$AUD = new self("AUD");
        self::$AZN = new self("AZN");
        self::$BSD = new self("BSD");
        self::$BHD = new self("BHD");
        self::$BDT = new self("BDT");
        self::$BBD = new self("BBD");
        self::$BYN = new self("BYN");
        self::$BZD = new self("BZD");
        self::$XOF = new self("XOF");
        self::$BMD = new self("BMD");
        self::$INR = new self("INR");
        self::$BTN = new self("BTN");
        self::$BOB = new self("BOB");
        self::$BOV = new self("BOV");
        self::$BAM = new self("BAM");
        self::$BWP = new self("BWP");
        self::$NOK = new self("NOK");
        self::$BRL = new self("BRL");
        self::$BND = new self("BND");
        self::$BGN = new self("BGN");
        self::$BIF = new self("BIF");
        self::$CVE = new self("CVE");
        self::$KHR = new self("KHR");
        self::$XAF = new self("XAF");
        self::$CAD = new self("CAD");
        self::$KYD = new self("KYD");
        self::$CLP = new self("CLP");
        self::$CLF = new self("CLF");
        self::$CNY = new self("CNY");
        self::$COP = new self("COP");
        self::$COU = new self("COU");
        self::$KMF = new self("KMF");
        self::$CDF = new self("CDF");
        self::$NZD = new self("NZD");
        self::$CRC = new self("CRC");
        self::$HRK = new self("HRK");
        self::$CUP = new self("CUP");
        self::$CUC = new self("CUC");
        self::$ANG = new self("ANG");
        self::$CZK = new self("CZK");
        self::$DKK = new self("DKK");
        self::$DJF = new self("DJF");
        self::$DOP = new self("DOP");
        self::$EGP = new self("EGP");
        self::$SVC = new self("SVC");
        self::$ERN = new self("ERN");
        self::$ETB = new self("ETB");
        self::$FKP = new self("FKP");
        self::$FJD = new self("FJD");
        self::$XPF = new self("XPF");
        self::$GMD = new self("GMD");
        self::$GEL = new self("GEL");
        self::$GHS = new self("GHS");
        self::$GIP = new self("GIP");
        self::$GTQ = new self("GTQ");
        self::$GBP = new self("GBP");
        self::$GNF = new self("GNF");
        self::$GYD = new self("GYD");
        self::$HTG = new self("HTG");
        self::$HNL = new self("HNL");
        self::$HKD = new self("HKD");
        self::$HUF = new self("HUF");
        self::$ISK = new self("ISK");
        self::$IDR = new self("IDR");
        self::$XDR = new self("XDR");
        self::$IRR = new self("IRR");
        self::$IQD = new self("IQD");
        self::$ILS = new self("ILS");
        self::$JMD = new self("JMD");
        self::$JPY = new self("JPY");
        self::$JOD = new self("JOD");
        self::$KZT = new self("KZT");
        self::$KES = new self("KES");
        self::$KPW = new self("KPW");
        self::$KRW = new self("KRW");
        self::$KWD = new self("KWD");
        self::$KGS = new self("KGS");
        self::$LAK = new self("LAK");
        self::$LBP = new self("LBP");
        self::$LSL = new self("LSL");
        self::$ZAR = new self("ZAR");
        self::$LRD = new self("LRD");
        self::$LYD = new self("LYD");
        self::$CHF = new self("CHF");
        self::$MOP = new self("MOP");
        self::$MKD = new self("MKD");
        self::$MGA = new self("MGA");
        self::$MWK = new self("MWK");
        self::$MYR = new self("MYR");
        self::$MVR = new self("MVR");
        self::$MRU = new self("MRU");
        self::$MUR = new self("MUR");
        self::$XUA = new self("XUA");
        self::$MXN = new self("MXN");
        self::$MXV = new self("MXV");
        self::$MDL = new self("MDL");
        self::$MNT = new self("MNT");
        self::$MAD = new self("MAD");
        self::$MZN = new self("MZN");
        self::$MMK = new self("MMK");
        self::$NAD = new self("NAD");
        self::$NPR = new self("NPR");
        self::$NIO = new self("NIO");
        self::$NGN = new self("NGN");
        self::$OMR = new self("OMR");
        self::$PKR = new self("PKR");
        self::$PAB = new self("PAB");
        self::$PGK = new self("PGK");
        self::$PYG = new self("PYG");
        self::$PEN = new self("PEN");
        self::$PHP = new self("PHP");
        self::$PLN = new self("PLN");
        self::$QAR = new self("QAR");
        self::$RON = new self("RON");
        self::$RUB = new self("RUB");
        self::$RWF = new self("RWF");
        self::$SHP = new self("SHP");
        self::$WST = new self("WST");
        self::$STN = new self("STN");
        self::$SAR = new self("SAR");
        self::$RSD = new self("RSD");
        self::$SCR = new self("SCR");
        self::$SLL = new self("SLL");
        self::$SGD = new self("SGD");
        self::$XSU = new self("XSU");
        self::$SBD = new self("SBD");
        self::$SOS = new self("SOS");
        self::$SSP = new self("SSP");
        self::$LKR = new self("LKR");
        self::$SDG = new self("SDG");
        self::$SRD = new self("SRD");
        self::$SZL = new self("SZL");
        self::$SEK = new self("SEK");
        self::$CHE = new self("CHE");
        self::$CHW = new self("CHW");
        self::$SYP = new self("SYP");
        self::$TWD = new self("TWD");
        self::$TJS = new self("TJS");
        self::$TZS = new self("TZS");
        self::$THB = new self("THB");
        self::$TOP = new self("TOP");
        self::$TTD = new self("TTD");
        self::$TND = new self("TND");
        self::$TRY = new self("TRY");
        self::$TMT = new self("TMT");
        self::$UGX = new self("UGX");
        self::$UAH = new self("UAH");
        self::$AED = new self("AED");
        self::$USN = new self("USN");
        self::$UYU = new self("UYU");
        self::$UYI = new self("UYI");
        self::$UYW = new self("UYW");
        self::$UZS = new self("UZS");
        self::$VUV = new self("VUV");
        self::$VES = new self("VES");
        self::$VND = new self("VND");
        self::$YER = new self("YER");
        self::$ZMW = new self("ZMW");
        self::$ZWL = new self("ZWL");
        self::$XBA = new self("XBA");
        self::$XBB = new self("XBB");
        self::$XBC = new self("XBC");
        self::$XBD = new self("XBD");
        self::$XTS = new self("XTS");
        self::$XXX = new self("XXX");
        self::$XAU = new self("XAU");
        self::$XPD = new self("XPD");
        self::$XPT = new self("XPT");
        self::$XAG = new self("XAG");
    }
}

Currency::init();
