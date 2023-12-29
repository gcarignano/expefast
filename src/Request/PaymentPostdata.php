<?php
/**
 * Copyright 2023 Connfido BV. All rights reserved.
 * Distribution of this software without explicit written consent from Newgen is
 * strictly prohibited. No part of this software must not be reverse engineered,
 * copied, reproduced or modified.
 */

namespace Gateway\Request;

use Gateway\HttpClient\GatewayRequest;
use Gateway\Response;
use Gateway\Entities\PaymentMethod;
use Gateway\Common\Locale;
use Gateway\Common\Fields;
use Gateway\Common\Messages;
use Gateway\Exceptions\BadUsage;

/**
 * Second Payment request (after clientAuth)
 *
 * @see Gateway\Request\AbstractRequest
 */
class PaymentPostdata extends AbstractRequest
{
    /**
     * @var \Gateway\Crypt\CertCrypt
     */
    protected $crypt;

    /**
     * @var string
     */
    protected $postUrl;

    /**
     * @var boolean
     */
    protected $asSync;

    /**
     * @param \Gateway\Entities\PaymentMethod $paymentMethod
     * @param \Gateway\Response $response
     * @param string $txnId
     * @param \Gateway\Common\Locale $lang
     */
    public function __construct(
        PaymentMethod $paymentMethod,
        Response $response,
        $txnId,
        Locale $lang,
        $asSync = false
    ) {
        parent::__construct($txnId);
        $this->authSignature = $paymentMethod->getReqAuthSignature();
        $this->crypt = $this->request->getCrypt();
        $this->lang = $lang;
        $this->postUrl = $this->crypt->decrypt(
            $response->getData()['payLoad']
        );
        $this->asSync = $asSync;
    }

    /**
     * @inheritdoc
     */
    public function getArray()
    {
        $gatewayReference = explode('/', $this->postUrl)[3];
        return array_merge(
            $this->request->getAuthHeaders($this->getId(), $this->authSignature),
            $this->merchant->getArray(),
            [
                Fields::GATEWAY_REFERENCE => $gatewayReference,
                Fields::SYNC => $this->asSync
            ]
        );
    }

    /**
     * Form post data for actual payment
     *
     * @return array
     */
    public function getPostFields()
    {
        return [
            'action' => $this->postUrl,
            'value'  => json_encode($this->request->getPostFields($this))
        ];
    }

    /**
     * {@inheritdoc}
     * @throws \Gateway\Exceptions\BadUsage
     */
    public function send()
    {
        throw new BadUsage(Messages::SEND_DISABLED);
    }
}
