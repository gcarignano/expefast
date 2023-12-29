<?php
/**
 * Copyright 2023 Connfido BV. All rights reserved.
 * Distribution of this software without explicit written consent from Newgen is
 * strictly prohibited. No part of this software must not be reverse engineered,
 * copied, reproduced or modified.
 */

namespace Gateway\Request;

use Gateway\Common\Currency;
use Gateway\Common\Fields;
use Gateway\Entities\DynamicDescriptor;
use Gateway\HttpClient\GatewayResponse;
use Gateway\Utility\Validator;

/**
 * Capture Transaction
 *
 * @see Gateway\Request\AbstractRequest
 */
class CaptureTransaction extends AbstractRequest
{
    /**
     * @inheritdoc
     */
    protected $authSignature = \Gateway\Common\GatewayConstants::API_TYPE_CAPTURE_PAYMENT;

    /**
     * @var \Gateway\Common\Currency
     */
    protected $currencyCode;

    /**
     * @var \Gateway\Entities\DynamicDescriptor
     */
    protected $dd;

    /**
     * @inheritdoc
     */
    protected $required = [
        'id', 'merchant', 'currencyCode'
    ];

    /**
     * @param string $txnId
     * @param \Gateway\Common\Currency $currency
     * @param \Gateway\Entities\DynamicDescriptor $dd
     */
    public function __construct($txnId, Currency $currency, DynamicDescriptor $dd = null)
    {
        parent::__construct($txnId);
        $this->currencyCode = $currency;
        $this->dd = $dd;
    }

    /**
     * @inheritdoc
     */
    public function getArray()
    {
        return array_merge(
            $this->merchant->getArray(),
            [
                Fields::TXN_CAPTURE => [
                    Fields::TXN_REFERENCE => $this->getId(),
                    Fields::CURRENCYCODE => $this->currencyCode->getValue()
                ]
            ],
            ($this->dd ? $this->dd->getArray() : [])
        );
    }

    /**
     * @inheritdoc
     */
    public function validate()
    {
        if ($this->dd instanceof DynamicDescriptor) {
            Validator::isValidEntity($this->dd);
        }
        return (
            parent::validate()
            && Validator::isAlnumSpecial($this->getId(), 100)
            && ($this->currencyCode instanceof Currency)
        );
    }

    /**
     * @inheritdoc
     */
    public function process(GatewayResponse $response)
    {
        $res = parent::process($response);
        if (($data = $res->getData())
            && isset($data['response']['responseCode'])
            && $data['response']['responseCode'] == 200
        ) {
            $res->setData($data['response']);
            $this->completed = true;
        }
        return $this->completed ? $res : $res->badResponse();
    }
}
