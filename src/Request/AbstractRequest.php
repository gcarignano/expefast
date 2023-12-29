<?php
/**
 * Copyright 2023 Connfido BV. All rights reserved.
 * Distribution of this software without explicit written consent from Newgen is
 * strictly prohibited. No part of this software must not be reverse engineered,
 * copied, reproduced or modified.
 */

namespace Gateway\Request;

use Gateway\HttpClient\GatewayRequest;
use Gateway\HttpClient\GatewayResponse;
use Gateway\Utility\Validator;
use Gateway\Entities\AbstractEntity;
use Gateway\Exceptions\ValidationException;
use Gateway\Exceptions\BadObject;
use Gateway\Exceptions\BadUsage;
use Gateway\Common\Messages;

/**
 * Generate valid requests for gateway and process gateway's response
 *
 * @see Gateway\Entities\AbstractEntity
 * @abstract
 */
abstract class AbstractRequest extends AbstractEntity
{
    /**
     * @var \Gateway\Common\Locale
     */
    protected $lang;

    /**
     * @var \Gateway\Entities\Merchant
     */
    protected $merchant;

    /**
     * @var \Gateway\HttpClient\GatewayRequest
     */
    protected $request;

    /**
     * @var boolean
     */
    protected $auth = true;

    /**
     * @var string
     */
    protected $authSignature;

    /**
     * @var boolean
     */
    protected $completed;

    /**
     * @var string
     */
    protected $method = "POST";

    /**
     * @param mixed $txnId
     * @throws \Gateway\Exceptions\BadObject
     */
    public function __construct($txnId)
    {
        parent::__construct($txnId);
        $this->merchant = \Gateway\App::getMerchant();
        $this->request = \Gateway\App::getRequest();
        $this->lang = \Gateway\App::getLang();
    }

    /**
     * Get language code
     *
     * @return string
     */
    public function getLang()
    {
        return $this->lang->getValue();
    }

    /**
     * Send the payload
     *
     * @return \Gateway\Response
     */
    public function send()
    {
        if (! $this->request instanceof GatewayRequest) {
            throw new BadObject(Messages::CLIENT_UNINITIALIZED);
        }
        $response = $this->request->sendRequest($this);
        return $this->process($response);
    }

    /**
     * Processes Gateway's Response
     *
     * @param GatewayResponse $response
     * @return \Gateway\Response
     */
    public function process(GatewayResponse $response)
    {
        return new \Gateway\Response($response);
    }

    /**
     * Get Endpoint for performing request
     *
     * @return string
     */
    public function getEndpoint()
    {
        return $this->merchant->getPaymentEndpoint();
    }

    /**
     * Generates payload in JSON Format
     *
     * @return string
     */
    public function getPayload()
    {
        if (! $this->validate()) {
            throw new ValidationException(Messages::REQUEST_INVALIDATED);
        }
        return json_encode($this->getArray());
    }

    /**
     * Checks if the request requires auth header to be included
     *
     * @return boolean
     */
    public function hasAuth()
    {
        return $this->auth;
    }

    /**
     * Check whether request has payload
     *
     * @return boolean
     */
    public function hasPayload()
    {
        return true;
    }

    /**
     * Returns HTTP Method for the currenct Request
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Get request signature for auth header generation
     *
     * @return string
     */
    public function getAuthSignature()
    {
        if (! $this->auth) {
            throw new BadUsage(Messages::AUTH_DISABLED);
        }
        return $this->authSignature;
    }
}
