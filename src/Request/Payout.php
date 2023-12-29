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
use Gateway\Entities\Transaction;
use Gateway\Entities\Url;
use Gateway\Entities\PaymentMethod;
use Gateway\Exceptions\ValidationException;
use Gateway\Common\Fields;

/**
 * Payout
 *
 * @see Gateway\Request\AbstractRequest
 */
class Payout extends AbstractRequest
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
     * @var boolean
     */
    protected $asSync = false;

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
        $customer,
        $email,
        PaymentMethod $paymentMethod,
        Url $url,
        $txnAmount,
        Currency $currencyCode,
        Locale $lang = null
    ) {
        parent::__construct($txnId);
        $this->customer = ($customer instanceof Customer) ? $customer : new Customer($customer);
        $this->email = $email;
        $this->customer->setIdRequired();
        $this->paymentMethod = $paymentMethod;
        $this->url = $url;
        $this->txn = new Transaction($txnId, $txnAmount, $currencyCode);
        if (! is_null($lang)) {
            $this->lang = $lang;
        }
    }

    /**
     * @inheritdoc
     */
    public function getArray()
    {
        return [
            Fields::LANGUAGE => $this->getLang(),
            Fields::MERCHANT => array_merge($this->merchant->getArray(), $this->customer->getCustomerIdField()),
            Fields::TXN => array_merge(
                $this->txn->getArray(),
                $this->paymentMethod->getArray(),
                [
                    Fields::PAYOUT => "true",
                    Fields::HOSTED_PAGE => !$this->paymentMethod->isWHPP(),
                    Fields::CUSTOMER_EMAIL => $this->email,
                    FIELDS::ASYNC => !$this->asSync
                ]
            ),
            Fields::URL => $this->url->getArray(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function validate()
    {
        if (! $this->lang instanceof Locale) {
            throw new ValidationException(Messages::LOCALE);
        }
        return parent::validate() && Validator::isEmail($this->email);
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

    public function sync()
    {
        $this->asSync = true;
        return $this;
    }
}
