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
class ApplePaySession extends AbstractRequest
{
    /**
     * @inheritdoc
     */
    protected $auth = false;

    /**
     * @var string
     */
    protected $url;

    /**
     * @inheritdoc
     */
    protected $required = [
        'merchant', 'url'
    ];

    /**
     * @param string $url
     */
    public function __construct($url)
    {
        parent::__construct(null);
        $this->url = $url;
    }

    /**
     * @inheritdoc
     */
    public function getArray()
    {
        return array_merge(
            $this->merchant->getArray(),
            [Fields::DOMAIN => $this->url]
        );
    }

    /**
     * @inheritdoc
     */
    public function process(GatewayResponse $response)
    {
        $res = parent::process($response);
        // $data = $res->getData();
        // if (() && isset($data[$this->currencyCode->getValue()])) {
        //     $res->setData($data[$this->currencyCode->getValue()]);
        // }
        $this->completed = true;
        return $this->completed ? $res : $res->badResponse(Messages::CURRENCY);
    }
}
