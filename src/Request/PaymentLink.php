<?php
/**
 * Copyright 2023 Connfido BV. All rights reserved.
 * Distribution of this software without explicit written consent from Newgen is
 * strictly prohibited. No part of this software must not be reverse engineered,
 * copied, reproduced or modified.
 */

namespace Gateway\Request;

use Gateway\HttpClient\GatewayResponse;
use Gateway\Utility\Validator;
use Gateway\Common\Locale;
use Gateway\Common\Currency;
use Gateway\Common\Messages;
use Gateway\Entities\Customer;
use Gateway\Entities\NotificationSetting;
use Gateway\Entities\Url;
use Gateway\Entities\CustomData;
use Gateway\Entities\DynamicDescriptor;
use Gateway\Entities\PaymentMethod;
use Gateway\Exceptions\ValidationException;
use Gateway\Common\Fields;
use Gateway\Common\PaymentMode;

/**
 * PaymentLink
 *
 * @see Gateway\Request\AbstractRequest
 */
class PaymentLink extends AbstractRequest
{
    /**
     * PaymentLink Query
     */
    const QUERY = 1;

    /**
     * PaymentLink Removal
     */
    const REMOVE = 2;

    /**
     * PaymentLink Create
     */
    const CREATE = 3;

    /**
     * PaymentLink Create
     */
    const UPDATE = 4;

    /**
     * @inheritdoc
     */
    protected $auth = false;

    /**
     * @var \Gateway\Entities\Customer
     */
    protected $customer;

    /**
     * @var \Gateway\Entities\Url
     */
    protected $url;

    /**
     * @var string
     */
    protected $txnAmount;

    /**
     * @var \Gateway\Common\Currency
     */
    protected $currencyCode;

    /**
     * @var boolean
     */
    protected $allow3d;

    /**
     * @var boolean
     */
    protected $billShip;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var \Gateway\Entities\NotificationSetting
     */
    protected $settings;

    /**
     * @var \Gateway\Entities\CustomData
     */
    protected $customData;

    /**
     * @var \Gateway\Entities\DynamicDescriptor
     */
    protected $dd;

    /**
     * @var \Gateway\Entities\PaymentMethod
     */
    protected $paymentMethod;

    
    /**
     * Type of PaymentLink related operation
     *
     * @var int
     */
    protected $operation = self::CREATE;

    /**
     * @inheritdoc
     */
    protected $required = [
        'lang', 'merchant', 'customer', 'url', 'settings'
    ];

    /**
     * @param \Gateway\Entities\Customer $customer
     * @param \Gateway\Entities\Url $url
     * @param string $txnAmount
     * @param \Gateway\Common\Currency $currencyCode
     * @param string $description
     * @param boolean $allowBillShip
     * @param \Gateway\Entities\NotificationSetting $settings
     * @param \Gateway\Common\Locale $lang
     */
    public function __construct(
        Customer $customer = null,
        Url $url = null,
        $txnAmount = null,
        Currency $currencyCode = null,
        $description = '',
        $allowBillShip = false,
        NotificationSetting $settings = null,
        Locale $lang = null
    ) {
        parent::__construct(null);
        $this->customer = $customer;
        $this->url = $url;
        $this->txnAmount = $txnAmount;
        $this->currencyCode = $currencyCode;
        $this->description = strval($description);
        $this->billShip = $allowBillShip;
        $this->settings = $settings ? $settings : new NotificationSetting();
        if (! is_null($lang)) {
            $this->lang = $lang;
        }
    }

    /**
     * Allow 3D payment
     *
     * @return \Gateway\Request\PaymentLink
     */
    public function allow3D()
    {
        $this->allow3d = true;
        return $this;
    }

    /**
     * Disallow 3D payment
     *
     * @return \Gateway\Request\PaymentLink
     */
    public function disallow3D()
    {
        $this->allow3d = false;
        return $this;
    }

    /**
     * Sets Custom Data for the transaction
     * @inheritdoc
     *
     * @param \Gateway\Entities\CustomData $param
     * @return \Gateway\Request\PaymentLink
     */
    public function setCustomData(CustomData $param = null)
    {
        $this->customData = $param;
        return $this;
    }

    /**
     * Set DynamicDescriptor
     *
     * @param DynamicDescriptor $dd
     * @return \Gateway\Request\PaymentLink
     */
    public function setDynamicDescriptor(DynamicDescriptor $dd)
    {
        $this->dd = $dd;
        return $this;
    }

    /**
     * Set Payment Mode
     *
     * @param PaymentMode $paymentMode
     * @return \Gateway\Request\PaymentLink
     */
    public function setPaymentMode(PaymentMode $paymentMode)
    {
        $this->paymentMethod = new PaymentMethod($paymentMode);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getArray()
    {
        $data = [
            Fields::LANGUAGE => $this->getLang(),
            Fields::CUSTOMER => array_merge(
                $this->customer->getArray(),
                $this->settings->getArray()
            ),
            Fields::TXN => [
                Fields::TXN_AMOUNT => $this->txnAmount,
                Fields::CURRENCYCODE => $this->currencyCode->getValue(),
                Fields::PAYMENTLINK => true,
                Fields::BILLSHIP => $this->billShip
            ],
            Fields::URL => $this->url->getArray(),
        ];
        if ($this->operation == self::UPDATE) {
            $data = array_merge($this->merchant->getArray(), $data);
        } else {
            $data[Fields::MERCHANT] = array_merge($this->merchant->getArray(), $this->customer->getCustomerIdField());
        }
        if (!empty($this->description)) {
            $data[Fields::TXN][Fields::PAYMENTLINK_DESCRIPTION] = $this->description;
        }
        if ($this->customData instanceof CustomData) {
            $data[Fields::CUSTOM_DATA] = $this->customData->getArray();
        }
        if ($this->dd instanceof DynamicDescriptor) {
            $data = array_merge($data, $this->dd->getArray());
        }
        if ($this->paymentMethod instanceof PaymentMethod) {
            $data[Fields::TXN] = array_merge($data[Fields::TXN], $this->paymentMethod->getArray());
        }
        if (is_bool($this->allow3d)) {
            $data[Fields::TXN][Fields::ALLOW3D] = $this->allow3d;
        }
        return $data;
    }

    /**
     * @inheritdoc
     */
    public function validate()
    {
        if (! $this->lang instanceof Locale) {
            throw new ValidationException(Messages::LOCALE);
        }
        if ($this->customData instanceof CustomData) {
            Validator::isValidEntity($this->customData);
        }
        if ($this->dd instanceof DynamicDescriptor) {
            Validator::isValidEntity($this->customData);
        }
        if ($this->dd instanceof DynamicDescriptor) {
            Validator::isValidEntity($this->dd);
        }
        if ($this->hasPayload()) {
            return (
                parent::validate()
                && Validator::isAmount($this->txnAmount)
                && ($this->currencyCode instanceof Currency)
                && Validator::isUniAlnumSpecial($this->description, 255, true)
            );
        }
        return parent::validate();
    }

    /**
     * @inheritdoc
     */
    public function getEndpoint()
    {
        $suffix = '';
        if ($this->operation != self::CREATE) {
            $suffix = '/' . $this->getId();
        }
        return $this->merchant->getPaymentLinkEndpoint() . $suffix;
    }

    /**
     * @inheritdoc
     */
    public function getMethod()
    {
        $method = [
            self::QUERY => "GET",
            self::REMOVE => "DELETE",
            self::CREATE => "POST",
            self::UPDATE => "PUT"
        ];
        return $method[$this->operation];
    }

    /**
     * @inheritdoc
     */
    public function hasPayload()
    {
        return !in_array($this->operation, [self::QUERY, self::REMOVE]);
    }

    public function details($linkId)
    {
        $this->setId($linkId);
        $this->operation = self::QUERY;
        return $this;
    }

    public function update($linkId)
    {
        $this->setId($linkId);
        $this->operation = self::UPDATE;
        return $this;
    }

    public function remove($linkId)
    {
        $this->setId($linkId);
        $this->operation = self::REMOVE;
        return $this;
    }
    /**
     * @inheritdoc
     */
    public function process(GatewayResponse $response)
    {
        $res = parent::process($response);
        $data = $res->getData();
        if ($this->operation == self::QUERY) {
            $res->setData(reset($data));
        }
        if (($this->operation == self::REMOVE
            && isset($data['response']['responseCode'])
            && in_array($data['response']['responseCode'], [200])
            ) || (isset($data['statusCode']) && $data['statusCode'] == 200)
        ) {
            $this->completed = true;
        }
        return $this->completed ? $res : $res->badResponse();
    }
}
