<?php
/**
 * Copyright 2023 Connfido BV. All rights reserved.
 * Distribution of this software without explicit written consent from Newgen is
 * strictly prohibited. No part of this software must not be reverse engineered,
 * copied, reproduced or modified.
 */

namespace Gateway\Request;

use Gateway\HttpClient\GatewayResponse;
use Gateway\Entities\Customer;
use Gateway\Entities\CardInfo;
use Gateway\Entities\CardInfoType;
use Gateway\Common\Fields;
use Gateway\Common\CardTypes;
use Gateway\Common\PaymentMode;

/**
 * Tokens
 *
 * @see Gateway\Request\AbstractRequest
 */
class Tokens extends AbstractRequest
{
    /**
     * Token Query
     */
    const QUERY = 1;


    /**
     * Token Removal
     */
    const REMOVE = 2;

    /**
     * Token Create
     */
    const CREATE = 3;

    /**
     * Token Verification
     */
    const VERIFY = 4;

    /**
     * Token Save
     */
    const SAVE = 5;

    /**
     * @var \Gateway\Entities\Customer
     */
    protected $customer;

    /**
     * Type of Token related operation
     *
     * @var int
     */
    protected $operation = self::QUERY;

    /**
     * @var \Gateway\Common\PaymentMode
     */
    protected $paymentMode;

    /**
     * @var \Gateway\Common\CardTypes
     */
    protected $cardType;

    /**
     * @var \Gateway\Entities\CardInfoType
     */
    protected $cardInfo;

    /**
     * @var boolean
     */
    protected $showAll;

    /**
     * @inheritdoc
     */
    protected $auth = false;

    /**
     * @inheritdoc
     */
    protected $required = [
        'merchant'
    ];

    /**
     * @param \Gateway\Entities\Customer|string $customer
     * @param \Gateway\Common\PaymentMode $paymentMode
     * @param \Gateway\Common\CardTypes $cardType
     * @param string $tokenId
     */
    public function __construct($customer, $tokenId = null, PaymentMode $paymentMode = null, CardTypes $cardType = null, $showAll = null)
    {
        parent::__construct($tokenId);
        $this->customer = ($customer instanceof Customer) ? $customer : new Customer($customer);
        $this->customer->setIdRequired();
        $this->paymentMode = $paymentMode;
        $this->cardType = $cardType;
        $this->showAll = $showAll;
    }

    /**
     * @inheritdoc
     */
    public function getArray()
    {
        $data = $this->merchant->getArray();
        if ($this->operation != self::VERIFY) {
            $data = array_merge(
                $data,
                $this->customer->getCustomerIdField()
            );
        }

        if (in_array($this->operation, [self::VERIFY, self::REMOVE])) {
            $data[Fields::CARD_TOKEN] = $this->getId();
        }
        if ($this->operation != self::REMOVE && $this->operation != self::CREATE) {
            if ($this->paymentMode != null) {
                $data[Fields::PAYMENT_MODE] = $this->paymentMode->getValue();
            }
            if ($this->cardType != null) {
                $data[Fields::CARDTYPE] = $this->cardType->getValue();
            }
            if ($this->showAll != null) {
                $showAllFlag = ($this->showAll == 'true');
                $data[Fields::SHOW_ALL_CARDS] = $showAllFlag;
            }
        }
        $key = [
            self::SAVE => Fields::TOKEN,
            self::CREATE => Fields::CARD
        ];
        if (in_array($this->operation, array_keys($key))) {
            if ($custArr = $this->customer->getArray()) {
                $data[Fields::CUSTOMER] = $custArr;
            }
            $data[$key[$this->operation]] = $this->cardInfo->getArray();
        }

        return $data;
    }

    /**
     * Remove token operation
     *
     * @return \Gateway\Request\Tokens
     */
    public function remove()
    {
        $this->operation = self::REMOVE;
        return $this;
    }

    /**
     * Verify token operation
     *
     * @return \Gateway\Request\Tokens
     */
    public function verify()
    {
        $this->operation = self::VERIFY;
        return $this;
    }

    /**
     * Set Card Info
     *
     * @return \Gateway\Request\Tokens
     */
    public function create(CardInfoType $cardData)
    {
        $this->cardInfo = $cardData;
        $this->operation = self::CREATE;
        $this->customer->disableShipping();

        return $this;
    }

    /**
     * Save Token
     *
     * @return \Gateway\Request\Tokens
     */
    public function save(CardInfo $cardData)
    {
        $this->cardInfo = $cardData;
        $this->operation = self::SAVE;
        $this->customer->disableShipping();

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function validate()
    {
        return (parent::validate()
            && (in_array($this->operation, [self::REMOVE, self::VERIFY]) ? !empty($this->getId()) : $this->customer->validate())
            && ($this->operation != self::VERIFY ? $this->customer->validate() : true));
    }

    /**
     * @inheritdoc
     */
    public function process(GatewayResponse $response)
    {
        $res = parent::process($response);
        $data = $res->getData();
        if (isset($data['tokens'])
            || (isset($data['response']['responseCode']) && in_array($data['response']['responseCode'], [200, 181]))
        ) {
            $this->completed = true;
        }
        return $this->completed ? $res : $res->badResponse();
    }
}
