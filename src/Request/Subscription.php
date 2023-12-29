<?php
/**
 * Copyright 2023 Connfido BV. All rights reserved.
 * Distribution of this software without explicit written consent from Newgen is
 * strictly prohibited. No part of this software must not be reverse engineered,
 * copied, reproduced or modified.
 */

namespace Gateway\Request;

use Gateway\Common\Fields;
use Gateway\Common\Messages;
use Gateway\Common\Locale;
use Gateway\Common\GatewayConstants;
use Gateway\Entities\Customer;
use Gateway\Entities\DynamicDescriptor;
use Gateway\Entities\NotificationSetting;
use Gateway\Entities\Url;
use Gateway\Entities\Transaction;
use Gateway\Exceptions\ValidationException;
use Gateway\HttpClient\GatewayResponse;
use Gateway\Utility\Validator;
use Gateway\Utility\Util;

class Subscription extends AbstractRequest
{
    /**
     * Subscription Query
     */
    const QUERY = 1;

    /**
     * Subscription Removal
     */
    const REMOVE = 2;

    /**
     * Subscription Create
     */
    const CREATE = 3;

    /**
     * Subscription Create
     */
    const UPDATE = 4;

    /**
     * Type of Subscription related operation
     *
     * @var int
     */
    protected $operation = self::CREATE;

    /**
     * @var \Gateway\Entities\DynamicDescriptor
     */
    protected $dd;

    /**
     * @var \Gateway\Entities\NotificationSetting
     */
    protected $settings;

    /**
     * @var string
     */
    protected $planId;

    /**
     * @var string
     */
    protected $txnId;

    /**
     * @var boolean
     */
    protected $allow3d;

    /**
     * @var boolean
     */
    protected $automaticDebit;

    /**
     * @var array
     */
    protected $url;

    /**
     * @var string
     */
    protected $startDate;

    /**
     * @var string
     */
    protected $expiry;

    /**
     * @var string
     */
    protected $qty;

    /**
     * @var string
     */
    protected $description;

    /**
     * @inheritdoc
     */
    protected $authSignature = GatewayConstants::API_TYPE_SUB_CREATE;

    /**
     * @inheritdoc
     */
    protected $required = [
        'lang', 'merchant', 'startDate', 'id', 'txnId', 'qty'
    ];

    /**
     * @inheritDoc
     */
    protected $external = false;

    public function __construct(
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
        $this->planId = ($planId instanceof SubscriptionPlan) ? $planId->getId() : $planId;
        parent::__construct(Util::generateGUID());
        $this->startDate = date(GatewayConstants::DATE_FORMAT, strtotime($startDate));
        $this->expiry = $expiry;
        $this->qty = $qty;
        $this->description = $description;
        $this->txnId = $txnId;
        $this->customer = $customer;
        $this->settings = $settings;
        if (!is_null($lang)) {
            $this->lang = $lang;
        }
    }

    public function setUrl(Url $url)
    {
        if (!is_null($url)) {
            $this->url = array_filter($url->getArray(), function ($field) {
                return in_array($field, [Fields::CONFIRMATIONPAGE, Fields::URL_PRIVACY, Fields::URL_TERMS]);
            }, ARRAY_FILTER_USE_KEY);
        }
        return $this;
    }

    /**
     * Set DynamicDescriptor
     *
     * @param DynamicDescriptor $dd
     * @return \Gateway\Request\Subscription
     */
    public function setDynamicDescriptor(DynamicDescriptor $dd)
    {
        $this->dd = $dd;
        return $this;
    }

    /**
     * Allow 3D payment
     *
     * @return \Gateway\Request\Subscription
     */
    public function allow3D()
    {
        $this->allow3d = true;
        return $this;
    }

    /**
     * Disallow 3D payment
     *
     * @return \Gateway\Request\Subscription
     */
    public function disallow3D()
    {
        $this->allow3d = false;
        return $this;
    }

    /**
     * Allow automatic debit
     *
     * @return \Gateway\Request\Subscription
     */
    public function allowAutomaticDebit()
    {
        $this->automaticDebit = true;
        return $this;
    }

    /**
     * Disallow automatic debit
     *
     * @return \Gateway\Request\Subscription
     */
    public function disallowAutomaticDebit()
    {
        $this->automaticDebit = false;
        return $this;
    }


    /**
     * Use external subscription
     *
     * @return Gateway\Request\Subscription
     */
    public function asExternal(){
        $this->external = true;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getArray()
    {
        $data = [
            Fields::LANGUAGE => $this->getLang(),
            Fields::MERCHANT => $this->merchant->getArray(),
            Fields::SUB_PLAN_ID => $this->planId,
            Fields::SUB_START_DATE => $this->startDate,
            Fields::SUB_QTY => $this->qty,
            Fields::SUB_EXTERNAL => $this->external,
            Fields::TXN => [
                Fields::TXN_REFERENCE => $this->txnId
            ]
        ];
        if ($this->description) {
            $data[Fields::SUB_DESCRIPTION] = $this->description;
        }
        if ($this->expiry) {
            $data[Fields::SUB_EXPIRY] = $this->expiry;
        }
        if (is_bool($this->allow3d)) {
            $data[Fields::TXN][Fields::ALLOW3D] = $this->allow3d;
        }
        if (is_bool($this->automaticDebit)) {
            $data[Fields::TXN][Fields::SUB_AUTOMATIC_DEBIT] = $this->automaticDebit;
        }
        if ($this->customer) {
            $data[Fields::CUSTOMER] = $this->customer->getArray();
            $data[Fields::MERCHANT] = array_merge(
                $data[Fields::MERCHANT],
                $this->customer->getCustomerIdField()
            );
        }
        if ($this->settings) {
            $data[Fields::CUSTOMER] = array_merge(
                $data[Fields::CUSTOMER],
                $this->settings->getArray()
            );
        }
        if ($this->url) {
            $data[Fields::URL] = $this->url;
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
        return (
            parent::validate()
            && Validator::isAlnumSpecial($this->getId(), 50)
            && Validator::isAlnumSpecial($this->planId, 50)
            && Validator::isAlnumSpecial($this->startDate, 100)
            && Validator::isNum($this->qty, 2)
            && Validator::isNum($this->expiry, 2, true)
            && Validator::isUniAlnumSpecial($this->description, 255, true)
        );
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

    /**
     * @inheritdoc
     */
    public function getEndpoint()
    {
        $suffix = '';
        if ($this->operation != self::CREATE) {
            $suffix = '/' . $this->getId();
        }
        return $this->merchant->getApiKeyEndpoint() . '/subscriptions' . $suffix;
    }

    public function details($subId)
    {
        $this->setId($subId);
        $this->operation = self::QUERY;
        $this->authSignature = GatewayConstants::API_TYPE_SUB_GET;
        return $this;
    }

    public function update($subId)
    {
        $this->setId($subId);
        $this->operation = self::UPDATE;
        $this->authSignature = GatewayConstants::API_TYPE_SUB_UPDATE;
        return $this;
    }

    public function remove($subId)
    {
        $this->setId($subId);
        $this->operation = self::REMOVE;
        $this->authSignature = GatewayConstants::API_TYPE_SUB_REMOVE;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function process(GatewayResponse $response)
    {
        $res = parent::process($response);
        $data = $res->getData();
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
