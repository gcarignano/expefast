<?php
/**
 * Copyright 2023 Connfido BV. All rights reserved.
 * Distribution of this software without explicit written consent from Newgen is
 * strictly prohibited. No part of this software must not be reverse engineered,
 * copied, reproduced or modified.
 */

namespace Gateway\Request;

use Gateway\Common\Fields;
use Gateway\Common\GatewayConstants;
use Gateway\Entities\Installments;
use Gateway\HttpClient\GatewayResponse;
use Gateway\Utility\Util;
use Gateway\Utility\Validator;

/**
 * SubscriptionPlan
 *
 * @see Gateway\Request\AbstractRequest
 */
class SubscriptionPlan extends AbstractRequest
{
    /**
     * SubscriptionPlan Query
     */
    const QUERY = 1;

    /**
     * SubscriptionPlan Removal
     */
    const REMOVE = 2;

    /**
     * SubscriptionPlan Create
     */
    const CREATE = 3;

    /**
     * SubscriptionPlan Create
     */
    const UPDATE = 4;

    /**
     * Type of SubscriptionPlan related operation
     *
     * @var int
     */
    protected $operation = self::CREATE;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var string
     */
    protected $code;

    /**
     * @var boolean
     */
    protected $carryForward;

    /**
     * @var string|int
     */
    protected $paymentFailureThreshold;

    /**
     * @var \Gateway\Entities\Installments
     */
    protected $installments;

    /**
     * @inheritdoc
     */
    protected $required = [
        'merchant', 'name', 'installments'
    ];

    /**
     * @inheritdoc
     */
    protected $authSignature = GatewayConstants::API_TYPE_SUB_PLAN_CREATE;

    public function __construct(
        $name = null,
        Installments $installments = null,
        $code = null,
        $description = null,
        $carryForward = null,
        $paymentFailureThreshold = null
    ) {
        parent::__construct(Util::generateGUID());
        $this->name = $name;
        $this->description = $description;
        $this->code = $code;
        $this->installments = $installments;
        $this->carryForward = filter_var($carryForward, FILTER_VALIDATE_BOOLEAN);
        $this->paymentFailureThreshold = $paymentFailureThreshold;
    }

    /**
     * @inheritdoc
     */
    public function getArray()
    {
        $data = [
            Fields::SUB_NAME => $this->name,
            Fields::SUB_INSTALLMENTS => $this->installments->getArray()
        ];
        if ($this->code && $this->operation == self::CREATE) {
            $data[Fields::SUB_CODE] = $this->code;
        }
        $optionals = [
            Fields::SUB_CARRY_FWD_AMOUNT => $this->carryForward,
            Fields::SUB_PAYMENT_FAIL_THRESHOLD => $this->paymentFailureThreshold,
            Fields::SUB_DESCRIPTION => $this->description
        ];
        foreach ($optionals as $name => $value) {
            if ($value) {
                $data[$name] = $value;
            }
        }
        return $data;
    }

    /**
     * @inheritdoc
     */
    public function validate()
    {
        return (
            parent::validate()
            && Validator::isUniAlnumSpecial($this->name, 150)
            && Validator::isUniAlnumSpecial($this->description, 255, true)
            && Validator::isUniAlnumSpecial($this->code, 50, true)
            && Validator::isNum($this->paymentFailureThreshold, 2, true)
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
        return $this->merchant->getApiKeyEndpoint() . '/plans' . $suffix;
    }

    public function details($subId)
    {
        $this->setId($subId);
        $this->operation = self::QUERY;
        $this->authSignature = GatewayConstants::API_TYPE_SUB_PLAN_GET;
        return $this;
    }

    public function update($subId)
    {
        $this->setId($subId);
        $this->operation = self::UPDATE;
        $this->authSignature = GatewayConstants::API_TYPE_SUB_PLAN_UPDATE;
        return $this;
    }

    public function remove($subId)
    {
        $this->setId($subId);
        $this->operation = self::REMOVE;
        $this->authSignature = GatewayConstants::API_TYPE_SUB_PLAN_REMOVE;
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
