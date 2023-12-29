<?php
/**
 * Copyright 2023 Connfido BV. All rights reserved.
 * Distribution of this software without explicit written consent from Newgen is
 * strictly prohibited. No part of this software must not be reverse engineered,
 * copied, reproduced or modified.
 */

namespace Gateway;

use Gateway\Entities\Merchant;
use Gateway\Entities\Customer;
use Gateway\Entities\Url;
use Gateway\Entities\ReportSetting;
use Gateway\Entities\CardInfo;
use Gateway\Entities\CardInfoType;
use Gateway\Entities\CompanyDetails as Details;
use Gateway\Entities\DynamicDescriptor;
use Gateway\Entities\Installments;
use Gateway\Entities\NotificationSetting;
use Gateway\Entities\CustomData;
use Gateway\Common\Currency;
use Gateway\Common\Locale;
use Gateway\Common\PaymentMode;
use Gateway\Common\CardTypes;
use Gateway\Entities\AbstractWireTransferBank;
use Gateway\Utility\Validator;

/**
 * Gateway PHP Library App
 */
final class App
{
    private static $merchant;

    private static $request;

    private static $crypt;

    private static $lang;

    private $customData;

    private $paymentMode;

    /**
     * @var \Gateway\Entities\DynamicDescriptor
     */
    protected $dd;

    public static function getMerchant()
    {
        if (!self::$merchant instanceof Merchant) {
            throw new Exceptions\BadObject("Merchant");
        }
        return self::$merchant;
    }

    public static function getRequest()
    {
        if (!self::$request instanceof HttpClient\GatewayRequest) {
            throw new Exceptions\BadObject("GatewayRequest");
        }
        return self::$request;
    }

    public static function getLang()
    {
        return self::$lang;
    }

    public function __construct(Merchant $merchant, Locale $lang = null)
    {
        Validator::isValidEntity($merchant);
        self::$merchant = $merchant;
        self::$lang = is_null($lang) ? Locale::$EN : $lang;
        self::$crypt = new Crypt\CertCrypt();
        self::$request = new HttpClient\GatewayRequest(self::$crypt);
    }

    public function payments($txnId, $amount, Customer $cust, Url $endpoints, Currency $currency)
    {
        $payments = new Payment($txnId, $amount, $cust, $endpoints, $currency);
	if ($this->customData !== null && $this->customData instanceof CustomData) {
            $payments->setCustomData($this->customData);
        }
	if ($this->dd !== null && $this->dd instanceof DynamicDescriptor) {
            $payments->setDynamicDescriptor($this->dd);
        }
        return $payments;
    }

    public function zeroAuth($txnId, Customer $cust, Url $endpoints, Currency $currency)
    {
        $payments = new Payment($txnId, 0, $cust, $endpoints, $currency);
        $payments->setZeroAuth();
        return $payments;
    }

    public function transactionDetails($txnId, $showCustomData = false)
    {
        return (new Request\TransactionDetails($txnId, $showCustomData))->send();
    }

    public function createRefund($txnId, $amount, $invoiceNo = null, $comment = null, DynamicDescriptor $dd = null)
    {
        $req = (new Request\Refund($txnId, $amount))
            ->setInvoiceNo($invoiceNo)
            ->setComment($comment);
        if ($dd) {
            $req->setDynamicDescriptor($dd);
        }
        return $req->send();
    }

    public function refundDetails($txnId)
    {
        return (new Request\RefundDetails($txnId))->send();
    }

    public function configuration(Currency $currency)
    {
        return (new Request\Configuration($currency))->send();
    }

    public function companyDetails(Details $details)
    {
        return (new Request\CompanyDetails($details))->send();
    }

    public function tokens($cust, PaymentMode $paymentMode = null, CardTypes $cardTypes = null, $showAll = false)
    {
        return (new Request\Tokens($cust, null, $paymentMode, $cardTypes, $showAll))->send();
    }

    public function getSavedCards($cust, PaymentMode $paymentMode = null, CardTypes $cardTypes = null)
    {
        return (new Request\Tokens($cust, null, $paymentMode, $cardTypes, true))->send();
    }

    public function saveCard(Customer $cust, CardInfoType $cardData)
    {
        return (new Request\Tokens($cust))->create($cardData)->send();
    }

    public function saveToken(Customer $cust, CardInfo $cardData)
    {
        return (new Request\Tokens($cust))->save($cardData)->send();
    }

    public function verifyToken($cust, $tokenId)
    {
        return (new Request\Tokens($cust, $tokenId))->verify()->send();
    }

    public function verifyCard($tokenId)
    {
        return (new Request\Tokens(null, $tokenId, null, null, true))->verify()->send();
    }

    public function deleteToken($cust, $tokenId)
    {
        return (new Request\Tokens($cust, $tokenId))->remove()->send();
    }

    public function report($fromDate, ReportSetting $setting = null, $toDate = null)
    {
        return (new Request\Report($fromDate, $setting, $toDate))->send();
    }

    public function voidTransaction($txnId, DynamicDescriptor $dd = null)
    {
        return (new Request\VoidTransaction($txnId, $dd))->send();
    }

    public function captureTransaction($txnId, Currency $currency, DynamicDescriptor $dd = null)
    {
        return (new Request\CaptureTransaction($txnId, $currency, $dd))->send();
    }

    public function webhook($id = null, $url = null, Common\Events ...$event)
    {
        return (new Request\Webhook($id, $url, ...$event));
    }

    public function generateApplepaySession($url)
    {
        return (new Request\ApplePaySession($url))->send();
    }

    public function createPaymentLink(
        Customer $cust,
        Url $url,
        $amount,
        Currency $currency,
        $description = '',
        $allowBillShip = false,
        NotificationSetting $settings = null,
        Locale $lang = null
    ) {
        $paymentLink = new Request\PaymentLink(
            $cust,
            $url,
            $amount,
            $currency,
            $description,
            $allowBillShip,
            $settings,
            $lang
        );
        if ($this->customData !== null && $this->customData instanceof CustomData) {
            $paymentLink->setCustomData($this->customData);
        }
        if ($this->dd !== null && $this->dd instanceof DynamicDescriptor) {
            $paymentLink->setDynamicDescriptor($this->dd);
        }
        if ($this->paymentMode !== null && $this->paymentMode instanceof PaymentMode) {
            $paymentLink->setPaymentMode($this->paymentMode);
        }
        return $paymentLink->send();
    }

    public function updatePaymentLink(
        $linkId,
        Customer $cust,
        Url $url,
        $amount,
        Currency $currency,
        $description = '',
        $allowBillShip = false,
        NotificationSetting $settings = null,
        Locale $lang = null
    ) {
        $paymentLink = new Request\PaymentLink(
            $cust,
            $url,
            $amount,
            $currency,
            $description,
            $allowBillShip,
            $settings,
            $lang
        );
        if ($this->customData !== null && $this->customData instanceof CustomData) {
            $paymentLink->setCustomData($this->customData);
        }
        if ($this->dd !== null && $this->dd instanceof DynamicDescriptor) {
            $paymentLink->setDynamicDescriptor($this->dd);
        }
        if ($this->paymentMode !== null && $this->paymentMode instanceof PaymentMode) {
            $paymentLink->setPaymentMode($this->paymentMode);
        }
        return $paymentLink->update($linkId)->send();
    }

    public function paymentLinkDetails($linkId)
    {
        return (new Request\PaymentLink())->details($linkId)->send();
    }

    public function removePaymentLink($linkId)
    {
        return (new Request\PaymentLink())->remove($linkId)->send();
    }

    public function createSubscriptionPlan(
        $name,
        Installments $installments,
        $code = null,
        $description = null,
        $carryForward = null,
        $paymentFailureThreshold = null
    ) {
        return (new Request\SubscriptionPlan(
            $name,
            $installments,
            $code,
            $description,
            $carryForward,
            $paymentFailureThreshold
        ))->send();
    }

    public function updateSubscriptionPlan(
        $subId,
        $name,
        Installments $installments = null,
        $description = null,
        $carryForward = null,
        $paymentFailureThreshold = null
    ) {
        return (new Request\SubscriptionPlan(
            $name,
            $installments,
            null,
            $description,
            $carryForward,
            $paymentFailureThreshold
        ))->update($subId)->send();
    }

    public function subscriptionPlanDetails($subId)
    {
        return (new Request\SubscriptionPlan())->details($subId)->send();
    }

    public function removeSubscriptionPlan($subId)
    {
        return (new Request\SubscriptionPlan())->remove($subId)->send();
    }

    public function subscription(
        $planId = null,
        $txnId = null,
        $startDate = null,
        $qty = null,
        $expiry = null,
        $description = null,
        Customer $customer = null,
        NotificationSetting $settings = null,
        Locale $lang = null
    ) {
        return (new Request\Subscription(
            $planId,
            $txnId,
            $startDate,
            $qty,
            $expiry,
            $description,
            $customer,
            $settings,
            $lang
        ));
    }

    public function wireTransfer(
        Currency $currencyCode = null,
        $accountHolderName = null,
        $accountHolderAddress = null,
        AbstractWireTransferBank $bank = null,
        $default = null,
        $description = null
    ) {
        return (new Request\WireTransfer(
            $currencyCode,
            $accountHolderName,
            $accountHolderAddress,
            $bank,
            $default,
            $description
        ));
    }

    public function approveWireTransferBankTransaction($txnId, $amount = null)
    {
        return (new Request\WireTransferBankTransaction($txnId, $amount))->send();
    }

    public function declineWireTransferBankTransaction($txnId)
    {
        return (new Request\WireTransferBankTransaction($txnId))->decline()->send();
    }

    public function generateHostedFields($customer, $columns)
    {
        return (new Request\HostedFields($customer, $columns))->send();
    }

    public function setCustomData(CustomData $data) {
        $this->customData = $data;
        return $this;
    }

    public function setDynamicDescriptor(DynamicDescriptor $dd)
    {
        $this->dd = $dd;
        return $this;
    }

    public function setPaymentMode(PaymentMode $paymentMode)
    {
        $this->paymentMode = $paymentMode;
        return $this;
    }
}
