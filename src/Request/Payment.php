<?php
/**
 * Copyright 2023 Connfido BV. All rights reserved.
 * Distribution of this software without explicit written consent from Newgen is
 * strictly prohibited. No part of this software must not be reverse engineered,
 * copied, reproduced or modified.
 */

namespace Gateway\Request;

use Gateway\Entities\Headers;
use Gateway\HttpClient\GatewayResponse;
use Gateway\Utility\Validator;
use Gateway\Common\Locale;
use Gateway\Common\Currency;
use Gateway\Common\Messages;
use Gateway\Entities\Customer;
use Gateway\Entities\CustomData;
use Gateway\Entities\DynamicDescriptor;
use Gateway\Entities\Transaction;
use Gateway\Entities\Url;
use Gateway\Entities\PaymentMethod;
use Gateway\Entities\Summary;
use Gateway\Entities\Items;
use Gateway\Exceptions\ValidationException;
use Gateway\Common\Fields;

/**
 * Payment
 *
 * @see Gateway\Request\AbstractRequest
 */
class Payment extends AbstractRequest
{
    /**
     * @var \Gateway\Entities\Customer
     */
    protected $customer;

    /**
     * @var \Gateway\Entities\Transaction
     */
    protected $txn;

    /**
     * @var \Gateway\Entities\Url
     */
    protected $url;

    /**
     * @var \Gateway\Entities\PaymentMethod
     */
    protected $paymentMethod;

    /**
     * @inheritdoc
     */
    protected $authSignature = \Gateway\Common\GatewayConstants::API_TYPE_CLIENT;

    /**
     * @var \Gateway\Entities\Summary
     */
    protected $summary;

    /**
     * @var \Gateway\Entities\Items
     */
    protected $items;

    /**
     * @var \Gateway\Entities\CustomData
     */
    protected $customData;

    /**
     * @var \Gateway\Entities\DynamicDescriptor
     */
    protected $dd;

    /**
     * @var string
     */
    protected $subId;

    /**
     * @var string
     */
    protected $order_id;

    /**
     * @var boolean
     */
    protected $asSync = false;

    /**
     * @var boolean
     */
    protected $requestPayerDetails = false;

    /**
     * @var string
     */
    protected $pageTag;

    /**
     * @var string
     */
    protected $executionDate;

    /**
     * @var string
     */
    protected $mobile;

    /**
     * @var boolean
     */
    protected $zeroAuth;

    /**
     * @var \Gateway\Entities\Headers
     */
    protected $headers;

    /**
     * @inheritdoc
     */
    protected $required = [
        'lang', 'merchant', 'customer', 'txn', 'url', 'paymentMethod'
    ];

    /**
     * @param string $txnId
     * @param \Gateway\Entities\Customer $customer
     * @param \Gateway\Entities\PaymentMethod $paymentMethod
     * @param \Gateway\Entities\Url $url
     * @param string $txnAmount
     * @param \Gateway\Common\Currency $currencyCode
     * @param \Gateway\Common\Locale $lang
     */
    public function __construct(
        $txnId,
        Customer $customer,
        PaymentMethod $paymentMethod,
        Url $url,
        $txnAmount,
        Currency $currencyCode,
        Locale $lang = null
    ) {
        parent::__construct($txnId);
        $this->customer = $customer;
        $this->customer->setAddressRequired();
        $this->paymentMethod = $paymentMethod;
        $this->url = $url;
        $this->txn = new Transaction($txnId, $txnAmount, $currencyCode);
        if (! is_null($lang)) {
            $this->lang = $lang;
        }
    }

    /**
     * Sets the Order ID
     *
     * @param string $orderId
     * @return \Gateway\Request\Payment
     */
    public function setOrderId($orderId)
    {
        $this->order_id = $orderId;
        return $this;
    }

    /**
     * Sets the Order Summary
     *
     * @param \Gateway\Entities\Summary $param
     * @return \Gateway\Request\Payment
     */
    public function setSummary(Summary $param = null)
    {
        $this->summary = $param;
        return $this;
    }

    /**
     * Sets the Order Items
     *
     * @param \Gateway\Entities\Items $param
     * @return \Gateway\Request\Payment
     */
    public function setItems(Items $param = null)
    {
        $this->items = $param;
        return $this;
    }

    /**
     * Set DynamicDescriptor
     *
     * @param DynamicDescriptor $dd
     * @return \Gateway\Request\Payment
     */
    public function setDynamicDescriptor(DynamicDescriptor $dd)
    {
        $this->dd = $dd;
        return $this;
    }

    /**
     * Set SubscriptionID
     *
     * @param string $subId
     * @return \Gateway\Request\Payment
     */
    public function setSubscriptionID($subId)
    {
        $this->subId = $subId;
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

    /**
     * Set Page tag
     *
     * @return \Gateway\Request\Payment
     */
    public function setPageTag($tag)
    {
        $this->pageTag = $tag;
        return $this;
    }

    /**
     * Sets Custom Data for the transaction
     * @inheritdoc
     *
     * @param \Gateway\Entities\CustomData $param
     * @return \Gateway\Request\Payment
     */
    public function setCustomData(CustomData $param = null)
    {
        $this->customData = $param;
        return $this;
    }

    /**
     * Set Execution Date
     *
     * @param string $date
     * @return \Gateway\Request\Payment
     */
    public function setExecutionDate($date)
    {
        $this->executionDate = date('Y-m-d H:i:s', strtotime($date));
        return $this;
    }
    /**
     * Set Zero Auth
     *
     * @param string $date
     * @return \Gateway\Request\Payment
     */
    public function setZeroAuth()
    {
        $this->txn->setZeroAuth();
        return $this;
    }
    /**
     * Set isApp flag
     *
     * @return \Gateway\Request\Payment
     */
    public function isApp()
    {
        $this->txn->asApp();
        return $this;
    }
    /**
     * Set Customer Mobile 
     *
     * @return \Gateway\Request\Payment
     */
    public function setCustomerMobile($mobile)
    {
        $this->mobile = $mobile;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getArray()
    {
        $data = [
            Fields::LANGUAGE => $this->getLang(),
            Fields::MERCHANT => array_merge($this->merchant->getArray(), $this->customer->getCustomerIdField()),
            Fields::CUSTOMER => $this->customer->getArray(),
            Fields::TXN => array_merge(
                $this->txn->getArray(),
                $this->paymentMethod->getArray(),
                [FIELDS::ASYNC => !$this->asSync],
                [FIELDS::REQUEST_PAYER_DETAILS => $this->requestPayerDetails]
            ),
            Fields::URL => $this->url->getArray(),
        ];
        if (! empty($this->order_id)) {
            $data[Fields::ORDER_ID] = $this->order_id;
        }
        if ($this->summary instanceof Summary) {
            $data[Fields::SUMMARY] = $this->summary->getArray();
        }
        if ($this->items instanceof Items) {
            $data[Fields::ITEM] = $this->items->getArray();
        }
        if ($this->customData instanceof CustomData) {
            $data[Fields::CUSTOM_DATA] = $this->customData->getArray();
        }
        if ($this->pageTag) {
            $data[Fields::TXN][Fields::TXN_PAGETAG] = $this->pageTag;
        }
        if ($this->dd) {
            $data = array_merge($data, $this->dd->getArray());
        }
        if ($this->subId) {
            $data[Fields::TXN][Fields::SUB_ID] = $this->subId;
        }
        if ($this->executionDate) {
            $data[Fields::TXN][Fields::EXECUTION_DATE] = $this->executionDate;
        }
        if ($this->mobile) {
            $data[Fields::TXN][Fields::CUSTOMER_MOBILE] = $this->mobile;
        }

        return $data;
    }

    /**
     * @inheritdoc
     */
    public function validate()
    {
        if ($this->summary instanceof Summary) {
            Validator::isValidEntity($this->summary);
        }
        if ($this->items instanceof Items) {
            Validator::isValidEntity($this->items);
        }
        if ($this->customData instanceof CustomData) {
            Validator::isValidEntity($this->customData);
        }
        if (! $this->lang instanceof Locale) {
            throw new ValidationException(Messages::LOCALE);
        }
        if ($this->asSync && !$this->paymentMethod->isWhpp()) {
            throw new ValidationException(Messages::ESYNC);
        }
        if ($this->dd instanceof DynamicDescriptor) {
            Validator::isValidEntity($this->dd);
        }
        return (parent::validate()
            && Validator::isAlnumSpecial($this->order_id, 100, true)
            && Validator::isAllSpecial($this->pageTag, 100, true)
            && Validator::isAlnumSpecial($this->subId, 50, true)
        );
    }

    /**
     * @inheritdoc
     */
    public function process(GatewayResponse $response)
    {
        $res = parent::process($response);
        if (isset($res->getData()['payLoad'])) {
            $postData = new PaymentPostdata($this->paymentMethod, $res, $this->getId(), $this->lang, $this->asSync);
            $formData = $postData->getPostFields();
            $res->setData($formData);
            $this->completed = true;
            if ($this->asSync) {
                $curlInstance = curl_init($formData['action']);
                curl_setopt($curlInstance, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curlInstance, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($curlInstance, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($curlInstance, CURLOPT_SSL_VERIFYPEER, false);
                if($this->headers) {
                curl_setopt($curlInstance, CURLOPT_HTTPHEADER, $this->headers->getArray());
                }
                curl_setopt($curlInstance, CURLOPT_POSTFIELDS, http_build_query(["data" => $formData['value']]));
                $this->merchant->curlOptions($curlInstance);
                $response = parent::process(new GatewayResponse(
                    curl_exec($curlInstance),
                    curl_getinfo($curlInstance, CURLINFO_HTTP_CODE),
                    curl_error($curlInstance)
                ));
                $response->setData(json_decode($response->raw(), true));
                $res = $response;
            }
        }
        return $this->completed ? $res : $res->badResponse();
    }
}
