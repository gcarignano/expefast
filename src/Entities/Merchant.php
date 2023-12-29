<?php
/**
 * Copyright 2023 Connfido BV. All rights reserved.
 * Distribution of this software without explicit written consent from Newgen is
 * strictly prohibited. No part of this software must not be reverse engineered,
 * copied, reproduced or modified.
 */

namespace Gateway\Entities;

use Gateway\Utility\Validator;
use Gateway\Utility\Util;
use Gateway\Common\Fields;
use Gateway\Common\Messages;
use Gateway\Exceptions\BadUsage;
use Gateway\Exceptions\BadObject;

/**
 * Merchant
 *
 * @see Gateway\Entities\AbstractEntity
 * @final
 */
final class Merchant extends AbstractEntity
{
    /**
     * API Token
     *
     * @var string
     */
    protected $apiToken;

    /**
     * API Endpoint
     *
     * @var string
     */
    protected $apiUrl;

    /**
     * Public Certificate file location
     *
     * @var string
     */
    protected $certFile;

    /**
     * Private Certificate file location
     *
     * @var string
     */
    protected $privateKeyFile;

    protected $proxy = [];

    /**
     * @inheritdoc
     */
    protected $required = [
        "id", "apiToken", "apiUrl", "certFile", "privateKeyFile"
    ];

    /**
     * @param string $apiKey
     * @param string $apiToken
     * @param string $certFile
     * @param string $privateKeyFile
     * @param string $endpoint
     */
    public function __construct(
        $apiKey,
        $apiToken,
        $certFile,
        $privateKeyFile,
        $apiRootEndpoint = \Gateway\Common\GatewayConstants::TEST_API_ENDPOINT
    ) {
        parent::__construct($apiKey);
        $this->apiToken = $apiToken;
        $this->apiUrl = Util::prependSchema($apiRootEndpoint);
        $this->certFile = realpath($certFile);
        $this->privateKeyFile = realpath($privateKeyFile);
    }

    /**
     * Enable Production endpoint
     *
     * @return \Gateway\Entities\Merchant
     */
    public function live()
    {
        $this->apiUrl = \Gateway\Common\GatewayConstants::API_ENDPOINT;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getArray()
    {
        return [
            Fields::MERCHANT_ID => $this->getId()
        ];
    }

    /**
     * @inheritdoc
     */
    public function validate()
    {
        if (preg_match("/partnerapi/i", $this->apiUrl)) {
            throw new BadUsage("Pass a valid Root API Endpoint.");
        }
        return (
            parent::validate()
            && Validator::isAlnumSpecial($this->getId(), 50)
            && Validator::isReadable($this->certFile)
            && Validator::isReadable($this->privateKeyFile)
            && Validator::isUrl($this->apiUrl)
        );
    }

    /**
     * Getter for API Token
     *
     * @return string
     */
    public function getApiToken()
    {
        return $this->apiToken;
    }

    /**
     * Getter for API URL
     *
     * @return string
     */
    public function getApiUrl()
    {
        return $this->apiUrl;
    }

    /**
     * Getter for Public Certificate file location
     *
     * @return string
     */
    public function getCertFile()
    {
        return $this->certFile;
    }

    /**
     * Getter for Private Certificate file location
     *
     * @return string
     */
    public function getPrivateKeyFile()
    {
        return $this->privateKeyFile;
    }

    /**
     * Getter for Payment API Endpoint
     *
     * @return string
     */
    public function getPaymentEndpoint()
    {
        return $this->getApiUrl() . "/partnerApi";
    }

    /**
     * Getter for PaymentLink API Endpoint
     *
     * @return string
     */
    public function getPaymentLinkEndpoint()
    {
        return $this->getApiUrl() . "/paymentLink/" . $this->getId();
    }

    /**
     * Getter for Generic API key Endpoint
     *
     * @return string
     */
    public function getApiKeyEndpoint()
    {
        return $this->getApiUrl() . "/" . $this->getId();
    }

    public function configureProxy($ip, $port, $username = "", $password = "", $tunnel = false)
    {
        $this->proxy = [
            "ip" => $ip,
            "port" => $port,
            "tunnel" => $tunnel,
            "username" => $username,
            "password" => $password
        ];
        return $this;
    }

    public function curlOptions(&$curlInstance)
    {
        if (empty($this->proxy)) {
            return;
        }
        if (! Validator::isCurl($curlInstance)) {
            throw new BadObject(Messages::CURL);
        }
        curl_setopt($curlInstance, CURLOPT_PROXY, $this->proxy["ip"] . ':' . $this->proxy["port"]);
        curl_setopt($curlInstance, CURLOPT_HTTPPROXYTUNNEL, $this->proxy["tunnel"]);
        if (!empty($this->proxy["username"])) {
            curl_setopt(
                $curlInstance,
                CURLOPT_PROXYUSERPWD,
                $this->proxy["username"] . ':' . $this->proxy["password"]
            );
        }
    }
}
