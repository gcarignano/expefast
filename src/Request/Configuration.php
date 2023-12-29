<?php
/**
 * Copyright 2023 Connfido BV. All rights reserved.
 * Distribution of this software without explicit written consent from Newgen is
 * strictly prohibited. No part of this software must not be reverse engineered,
 * copied, reproduced or modified.
 */

namespace Gateway\Request;

use Gateway\HttpClient\GatewayResponse;
use Gateway\Response;
use Gateway\Common\Currency;
use Gateway\Common\Fields;
use Gateway\Common\Messages;

/**
 * Merchant's Configuration
 *
 * @see Gateway\Request\AbstractRequest
 */
class Configuration extends AbstractRequest
{
    /**
     * @inheritdoc
     */
    protected $auth = false;

    /**
     * @var \Gateway\Common\Currency
     */
    protected $currencyCode;

    /**
     * @inheritdoc
     */
    protected $required = [
        'merchant', 'currencyCode'
    ];

    /**
     * @param \Gateway\Common\Currency $currencyCode
     */
    public function __construct(Currency $currencyCode)
    {
        parent::__construct(null);
        $this->currencyCode = $currencyCode;
    }

    /**
     * @inheritdoc
     */
    public function getArray()
    {
        return array_merge(
            $this->merchant->getArray(),
            [ Fields::CURRENCYCODE => $this->currencyCode->getValue() ]
        );
    }

    /**
     * @inheritdoc
     */
    public function validate()
    {
        return (
            parent::validate()
            && ($this->currencyCode instanceof Currency)
        );
    }

    /**
     * @inheritdoc
     */
    public function process(GatewayResponse $response)
    {
        $res = parent::process($response);
        if (($data = $res->getData()) && isset($data[$this->currencyCode->getValue()])) {
            $res->setData($data[$this->currencyCode->getValue()]);
            $this->completed = true;
        }
        return $this->completed ? $res : $res->badResponse(Messages::CURRENCY);
    }
}
