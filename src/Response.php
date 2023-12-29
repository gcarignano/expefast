<?php
/**
 * Copyright 2023 Connfido BV. All rights reserved.
 * Distribution of this software without explicit written consent from Newgen is
 * strictly prohibited. No part of this software must not be reverse engineered,
 * copied, reproduced or modified.
 */

namespace Gateway;

use Gateway\HttpClient\GatewayResponse;
use Gateway\Common\GatewayConstants;
use Gateway\Exceptions\BadObject;

final class Response
{
    /**
     * @var \Gateway\HttpClient\GatewayResponse
     */
    private $response;

    /**
     * @var mixed
     */
    private $data;

    /**
     * @var boolean
     */
    private $success = true;

    /**
     * @var string
     */
    private $errorDesc;

    /**
     * @var string
     */
    private $errorCode;

    /**
     * @var string
     */
    private $httpCode;

    /**
     * @param \Gateway\HttpClient\GatewayResponse $response
     */
    public function __construct(GatewayResponse $response)
    {
        $this->response = $response;
        $this->httpCode = $this->response->httpCode();
        $this->data = json_decode($this->response->getResponse(), true);
        if (json_last_error() != JSON_ERROR_NONE) {
            throw new BadObject(
                "Invalid JSON (" . json_last_error() . ") : "
                . $this->response->getResponse()
            );
        }
        if ($this->response->hasError()) {
            $this->success = false;
        }
    }

    /**
     * Get HTTP status code
     *
     * @return string
     */
    public function httpCode()
    {
        return $this->httpCode;
    }

    /**
     * Check errorneous response
     *
     * @return boolean
     */
    public function hasError()
    {
        return !$this->success;
    }

    /**
     * Sets the response as a bad response
     *
     * @param string $errorDesc
     * @param string $errorCode
     * @return \Gateway\Response
     */
    public function badResponse($errorDesc = null, $errorCode = null)
    {
        $this->success = false;
        if ($errorCode && !$this->errorCode) {
            $this->errorCode = $errorCode;
        }
        if ($errorDesc && !$this->errorDesc) {
            $this->errorDesc = $errorDesc;
        }
        return $this;
    }

    /**
     * Get error description
     *
     * @return string
     */
    public function getDescription()
    {
        if (empty($this->errorDesc)) {
            if (isset($this->data['description'])) {
                $this->errorDesc = $this->data['description'];
            }
            if (isset($this->data['response']['description'])) {
                $this->errorDesc = $this->data['response']['description'];
            }
        }
        return $this->errorDesc;
    }

    /**
     * Get raw response
     *
     * @return string
     */
    public function raw()
    {
        return $this->response->getResponse();
    }

    /**
     * Get error code
     *
     * @return string
     */
    public function getError()
    {
        if (empty($this->errorCode)) {
            $this->errorCode = $this->httpCode;
            if (isset($this->data['responseCode'])) {
                $this->errorCode = $this->data['responseCode'];
            }
            if (isset($this->data['response']['responseCode'])) {
                $this->errorCode = $this->data['response']['responseCode'];
            }
        }
        return $this->errorCode;
    }

    /**
     * Get response data
     *
     * @return mixed
     */
    public function getData()
    {
        return ($this->hasError() ? null : $this->data);
    }

    protected function isPayment()
    {
        return (!$this->hasError()
            && isset($this->getData()['action'])
            && isset($this->getData()['value'])
        );
    }

    /**
     * Redirect client if possible
     *
     * @return \Gateway\Response
     */
    public function redirect()
    {
        if ($this->isPayment()) {
            $redirectForm = base64_decode(GatewayConstants::PAYMENT_FORM);
            $redirectForm = preg_replace('/POSTURL/', $this->getData()['action'], $redirectForm);
            $redirectForm = preg_replace('/POSTDATA/', $this->getData()['value'], $redirectForm);
            echo $redirectForm;
        }
        return $this;
    }

    public function openIFrame($frameWrapperClass = "")
    {
        if ($this->isPayment()) {
            echo '<script> window.onload = function () { ' .
                file_get_contents(dirname(__FILE__) . '/js/iframe-sdk.js')
                . sprintf(
                    "__gatewayIFrame.initPaymentIFrame('%s', '%s', '%s');",
                    $this->getData()['value'],
                    $this->getData()['action'],
                    $frameWrapperClass
                ) . '} </script>';
        }
        return $this;
    }

    /**
     * Set/update response data
     *
     * @param mixed $data
     * @return \Gateway\Response
     */
    public function setData($data)
    {
        if (! $this->hasError()) {
            $this->data = $data;
        }
        return $this;
    }
}
