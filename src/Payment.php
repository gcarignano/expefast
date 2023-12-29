<?php
/**
 * Copyright 2023 Connfido BV. All rights reserved.
 * Distribution of this software without explicit written consent from Newgen is
 * strictly prohibited. No part of this software must not be reverse engineered,
 * copied, reproduced or modified.
 */

namespace Gateway;

use Gateway\Entities\PaymentMethod;
use Gateway\Entities\WhppInfoInterface;
use Gateway\Entities\CardInfo;
use Gateway\Entities\BankInfo;
use Gateway\Entities\Customer;
use Gateway\Entities\CustomData;
use Gateway\Entities\DynamicDescriptor;
use Gateway\Entities\Url;
use Gateway\Entities\Summary;
use Gateway\Entities\ThreeDS;
use Gateway\Entities\Items;
use Gateway\Entities\Headers;
use Gateway\Exceptions\ValidationException;
use Gateway\Common\GatewayConstants;
use Gateway\Common\Currency;
use Gateway\Common\Fields;
use Gateway\Common\Locale;
use Gateway\Common\Messages;
use Gateway\Common\PaymentMode;
use Gateway\Common\CardTypes;
use Gateway\Common\Banks;

/**
 * Payment class
 */
final class Payment
{
    private $lang;

    private $customer;

    private $url;

    private $amount;

    private $txnId;

    private $currencyCode;

    private $summary;

    private $items;

    private $customData;

    private $order_id;

    private $asSync;

    private $allow3d;

    private $pageTag;

    private $verify;

    private $dd;

    private $subId;

    private $executionDate;

    private $zeroAuth;

    private $requestPayerDetails;

    private $headers;

    private $isApp;

    private $mobile;

    public function __construct($txnId, $amount, Customer $cust, Url $url, Currency $currencyCode)
    {
        $this->txnId = $txnId;
        $this->customer = $cust;
        $this->url = $url;
        $this->amount = $amount;
        $this->currencyCode = $currencyCode;
    }

    private function create(PaymentMethod $paymentMethod)
    {
        $payment = (new Request\Payment($this->txnId, $this->customer, $paymentMethod, $this->url, $this->amount, $this->currencyCode, $this->lang))
            ->setSummary($this->summary)
            ->setItems($this->items)
            ->setCustomData($this->customData)
            ->setOrderId($this->order_id)
            ->setPageTag($this->pageTag);
        if ($this->asSync) {
            if($this->headers) {
                $payment->sync($this->headers);
            } else {
            $payment->sync();
        }
        }
        if (is_bool($this->allow3d)) {
            $paymentMethod->{$this->allow3d ? 'allow3D' : 'disallow3D'}();
        }
        if ($this->dd) {
            $payment->setDynamicDescriptor($this->dd);
        }
        if ($this->subId) {
            $payment->setSubscriptionID($this->subId);
        }
        if($this->executionDate){
            $payment->setExecutionDate($this->subId);
        }
        if($this->zeroAuth){
            $payment->setZeroAuth();
        }
        if ($this->isApp) {
            $payment->isApp();
        }
        if ($this->requestPayerDetails) {
            $payment->requestPayerDetails();
        }
        if ($this->mobile) {
            $payment->setCustomerMobile($this->mobile);
        }
        return $payment->send();
    }

    public function createPayout($email, PaymentMode $mode = null, WhppInfoInterface $whpp = null)
    {
        $_payMethod = new PaymentMethod($mode);
        $_payMethod->asPayout();
        if (! is_null($whpp)) {
            $_payMethod->addWhppInfo($whpp);
        }
        $payment = new Request\Payout(
            $this->txnId,
            $this->customer,
            $email,
            $_payMethod,
            $this->url,
            $this->amount,
            $this->currencyCode,
            $this->lang
        );
        if ($this->asSync) {
            $payment->sync();
        }
        return $payment->send();
    }

    public function setLang(Locale $lang)
    {
        $this->lang = $lang;
        return $this;
    }

    public function setDynamicDescriptor(DynamicDescriptor $dd)
    {
        $this->dd = $dd;
        return $this;
    }

    public function setSubscriptionID($subId)
    {
        $this->subId = $subId;
        return $this;
    }

    public function setExecutionDate($date)
    {
        $this->executionDate = date('Y-m-d H:i:s', strtotime($date));
        return $this;
    }

    public function sync(Headers $headers = null)
    {
        $this->asSync = true;
        $this->headers = $headers;
        return $this;
    }

    public function requestPayerDetails()
    {
        $this->requestPayerDetails = true;
        return $this;
    }

    public function allow3D()
    {
        $this->allow3d = true;
        return $this;
    }

    public function disallow3D()
    {
        $this->allow3d = false;
        return $this;
    }

    public function verify()
    {
        $this->verify = true;
        return $this;
    }

    public function setPageTag($tag)
    {
        $this->pageTag = $tag;
        return $this;
    }

    public function setOrderId($order_id)
    {
        $this->order_id = $order_id;
        return $this;
    }

    public function setOrderSummary(Summary $orderSummary)
    {
        $this->summary = $orderSummary;
        return $this;
    }

    public function setOrderItems(Items $orderItems)
    {
        $this->items = $orderItems;
        return $this;
    }

    public function setCustomData(CustomData $param)
    {
        $this->customData = $param;
        return $this;
    }

    public function setZeroAuth()
    {
        $this->zeroAuth = true;
        return $this;
    }

    public function isApp()
    {
        $this->isApp = true;
        return $this;
    }

    public function createApplePay($paymentToken = null)
    {
        $_paymentMethod = new PaymentMethod(PaymentMode::$APPLE_PAY);
        if (!empty($paymentToken)) {
            $_paymentMethod->addAdditionalData(
                new Entities\AlternateCardInfo($paymentToken)
            );
        }
        return $this->create($_paymentMethod);
    }

    public function createAstropay()
    {
        return $this->create(new PaymentMethod(PaymentMode::$ASTROPAY));
    }

    public function createAstropayCard()
    {
        return $this->create(new PaymentMethod(PaymentMode::$ASTROPAYCARD));
    }

    public function createBanamex()
    {
        return $this->create(new PaymentMethod(PaymentMode::$BANAMEX));
    }

    private function createBank(PaymentMode $mode, BankInfo $bankOptions = null)
    {
        $_payMethod = new PaymentMethod($mode);
        if (! is_null($bankOptions)) {
            $_payMethod->addWhppInfo($bankOptions);
        }
        return $this->create($_payMethod);
    }

    public function createBCMC()
    {
        $_payMethod = new PaymentMethod(PaymentMode::$BCMC);
        return $this->create($_payMethod);
    }

    public function createBoku()
    {
        return $this->create(new PaymentMethod(PaymentMode::$BOKU));
    }

    public function createBoleto()
    {
        return $this->create(new PaymentMethod(PaymentMode::$BOLETO));
    }

    public function createChinaunion()
    {
        $_payMethod = new PaymentMethod(PaymentMode::$CHINAUNIONPAY);
        return $this->create($_payMethod);
    }

    private function createCard(
        PaymentMode $mode,
        CardTypes $cardType = null,
        CardInfo $cardOptions = null,
        ThreeDS $tds = null
    ) {
        $_payMethod = new PaymentMethod($mode, $cardType);
        if ($tds) {
            $_payMethod->add3DS($tds);
        }
        if (! is_null($cardOptions)) {
            if ($this->verify) {
                $co = $cardOptions->getArray();
                if (
                    !empty($co[Fields::CARD_TOKEN])
                    && !empty($co[Fields::CARD_CVV])
                ) {
                    $res = (new Request\Tokens(null, $co[Fields::CARD_TOKEN], null, null, true))
                        ->verify()->send();
                    if ($res->hasError()) {
                        throw new ValidationException(Messages::BAD_TOKEN . ": " . $co[Fields::CARD_TOKEN]);
                    }
                }
            }
            $_payMethod->addWhppInfo($cardOptions);
        }
        return $this->create($_payMethod);
    }

    public function createCreditCard(CardTypes $cardType = null, CardInfo $cardOptions = null, ThreeDS $tds = null)
    {
        return $this->createCard(PaymentMode::$CREDITCARD, $cardType, $cardOptions, $tds);
    }

    public function createDebitCard(CardTypes $cardType = null, CardInfo $cardOptions = null, ThreeDS $tds = null)
    {
        return $this->createCard(PaymentMode::$DEBITCARD, $cardType, $cardOptions, $tds);
    }

    public function createEPS()
    {
        $_payMethod = new PaymentMethod(PaymentMode::$EPS);
        return $this->create($_payMethod);
    }

    public function createGiropay(BankInfo $bankOptions = null)
    {
        return $this->createBank(PaymentMode::$GIROPAY, $bankOptions);
    }

    public function createHPP(ThreeDS $tds = null)
    {
        $_payMethod = new PaymentMethod();
        if ($tds) {
            $_payMethod->add3DS($tds);
        }
        return $this->create($_payMethod);
    }

    public function createIDEAL(Banks $bankCode = null)
    {
        $_payMethod = new PaymentMethod(PaymentMode::$IDEAL, $bankCode);
        return $this->create($_payMethod);
    }

    public function createInterac()
    {
        $_payMethod = new PaymentMethod(PaymentMode::$INTERAC);
        return $this->create($_payMethod);
    }

    public function createInteracTransfer()
    {
        $_payMethod = new PaymentMethod(PaymentMode::$INTERAC_TRANSFER);
        return $this->create($_payMethod);
    }

    public function createKlarna()
    {
        $_payMethod = new PaymentMethod(PaymentMode::$KLARNA);
        return $this->create($_payMethod);
    }

    public function createKlarnaInvoice()
    {
        return $this->create(new PaymentMethod(PaymentMode::$KLARNA_INVOICE));
    }

    public function createMercadolivre()
    {
        return $this->create(new PaymentMethod(PaymentMode::$MERCADOLIVRE));
    }

    public function createMoneta()
    {
        return $this->create(new PaymentMethod(PaymentMode::$MONETA));
    }

    public function createMuchBetter()
    {
        $_payMethod = new PaymentMethod(PaymentMode::$MUCH_BETTER);
        return $this->create($_payMethod);
    }

    public function createMultibanco()
    {
        $_payMethod = new PaymentMethod(PaymentMode::$MULTIBANCO);
        return $this->create($_payMethod);
    }

    public function createNeteller()
    {
        return $this->create(new PaymentMethod(PaymentMode::$NETELLER));
    }

    public function createOxxo()
    {
        return $this->create(new PaymentMethod(PaymentMode::$OXXO));
    }

    public function createPaypal()
    {
        $_payMethod = new PaymentMethod(PaymentMode::$PAYPAL);
        return $this->create($_payMethod);
    }

    public function createPaysafecard()
    {
        $_payMethod = new PaymentMethod(PaymentMode::$PAYSAFECARD);
        return $this->create($_payMethod);
    }

    public function createPicPay($documentId = null, $documentType = null)
    {
        $_paymentMethod = new PaymentMethod(PaymentMode::$PICPAY);
        if (!empty($documentId) && !empty($documentType)) {
            $_paymentMethod->addAdditionalData(
                new Entities\AlternateInfo($documentId, $documentType)
            );
        }
        return $this->create($_paymentMethod);
    }

    public function createPIX($documentId = null, $documentType = null)
    {
        $_paymentMethod = new PaymentMethod(PaymentMode::$PIX);
        if (!empty($documentId) && !empty($documentType)) {
            $_paymentMethod->addAdditionalData(
                new Entities\AlternateInfo($documentId, $documentType)
            );
        }
        return $this->create($_paymentMethod);
    }

    public function createPlugnPay()
    {
        return $this->create(new PaymentMethod(PaymentMode::$PNP));
    }

    public function createSEPA(BankInfo $bankOptions = null)
    {
        return $this->createBank(PaymentMode::$SEPA, $bankOptions);
    }

    public function createSkrill()
    {
        $_payMethod = new PaymentMethod(PaymentMode::$SKRILL);
        return $this->create($_payMethod);
    }

    public function createSOFORT()
    {
        $_payMethod = new PaymentMethod(PaymentMode::$SOFORT);
        return $this->create($_payMethod);
    }

    public function createSOLO()
    {
        $_payMethod = new PaymentMethod(PaymentMode::$SOLO);
        return $this->create($_payMethod);
    }

    public function createTED()
    {
        $_payMethod = new PaymentMethod(PaymentMode::$TED);
        return $this->create($_payMethod);
    }

    public function createTrustly()
    {
        $_payMethod = new PaymentMethod(PaymentMode::$TRUSTLY);
        return $this->create($_payMethod);
    }

    public function createWireTransfer($registrationId = null)
    {
        $_paymentMethod = new PaymentMethod(PaymentMode::$WIRE_TRANSFER);
        if (!empty($registrationId)) {
            $_paymentMethod->addAdditionalData(
                new Entities\WireTransferInfo($registrationId)
            );
        }
        return $this->create($_paymentMethod);
    }

    public function createTrustlyDirectDebit()
    {
        return $this->create(new PaymentMethod(PaymentMode::$TRUSTLYDD));
    }

    public function createRapidTransfer()
    {
        return $this->create(new PaymentMethod(PaymentMode::$RAPID_TRANSFER));
    }

    public function createInvoiceMe()
    {
        return $this->create(new PaymentMethod(PaymentMode::$INVOICEME));
    }

    public function createGooglePay($paymentToken = null, ThreeDS $tds = null)
    {
        $_paymentMethod = new PaymentMethod(PaymentMode::$GOOGLEPAY);
        if (!empty($paymentToken)) {
            $_paymentMethod->addAdditionalData(
                new Entities\AlternateCardInfo($paymentToken)
            );
        }
        if ($tds) {
            $_paymentMethod->add3DS($tds);
        }
        return $this->create($_paymentMethod);
    }

    public function createVipPass()
    {
        return $this->create(new PaymentMethod(PaymentMode::$VIP_PASS));
    }
    public function createUpi()
    {
        return $this->create(new PaymentMethod(PaymentMode::$UPI));
    }
    public function createSwish($mobile = null)
    {
        $this->mobile = $mobile;
        return $this->create(new PaymentMethod(PaymentMode::$SWISH));
    }
}
