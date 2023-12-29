<?php
/**
 * Copyright 2023 Connfido BV. All rights reserved.
 * Distribution of this software without explicit written consent from Newgen is
 * strictly prohibited. No part of this software must not be reverse engineered,
 * copied, reproduced or modified.
 */

namespace Gateway\HttpClient;

/**
 * Handles Gateway response
 */
class GatewayResponse
{
    /**
     * Response of Gateway Request
     *
     * @var string
     */
    private $response;

    /**
     * @var int
     */
    private $httpStatusCode;

    /**
     * Error Description
     *
     * @var string
     */
    private $description;

    /**
     * @param string $response
     * @param int $httpStatusCode
     */
    public function __construct($response, $httpStatusCode = 200, $description = '')
    {
        $this->response = $response;
        $this->httpStatusCode = $httpStatusCode;
        $this->description = $description;
    }

    /**
     * Returns the gateway response
     *
     * @return string
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Check if gateway response has some error
     *
     * @return boolean
     */
    public function hasError()
    {
        return preg_match('/^(?!2)/', $this->httpStatusCode);
    }

    /**
     * Returns HTTP Status code
     *
     * @return int
     */
    public function httpCode()
    {
        return $this->httpStatusCode;
    }

    /**
     * Returns HTTP Error Description
     *
     * @return int
     */
    public function getErrorDescription()
    {
        return $this->description;
    }
}
