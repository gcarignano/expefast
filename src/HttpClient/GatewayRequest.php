<?php
/**
 * Copyright 2023 Connfido BV. All rights reserved.
 * Distribution of this software without explicit written consent from Newgen is
 * strictly prohibited. No part of this software must not be reverse engineered,
 * copied, reproduced or modified.
 */

namespace Gateway\HttpClient;

use Gateway\Request\AbstractRequest;
use Gateway\Crypt\CryptInterface;
use Gateway\Common\GatewayConstants;

/**
 * Handles Gateway Request
 */
class GatewayRequest
{
    /**
     * @var \Gateway\Entities\Merchant
     */
    private $merchant;

    /**
     * @var \Gateway\Crypt\CryptInterface
     */
    private $crypt;

    /**
     * @param \Gateway\Crypt\CryptInterface $crypt
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function __construct(CryptInterface $crypt)
    {
        $this->crypt = $crypt;
        $this->merchant = \Gateway\App::getMerchant();
    }

    /**
     * Sends the request to gateway
     *
     * @param \Gateway\Request\AbstractRequest $request
     * @return \Gateway\HttpClient\GatewayResponse
     */
    public function sendRequest(AbstractRequest $request)
    {
        $curlInstance = $this->getCurlInstance($request);
        if ($request->hasPayload()) {
            curl_setopt($curlInstance, CURLOPT_POSTFIELDS, http_build_query($this->getPostFields($request)));
        }
        return new GatewayResponse(
            curl_exec($curlInstance),
            curl_getinfo($curlInstance, CURLINFO_HTTP_CODE),
            curl_error($curlInstance)
        );
    }

    /**
     * Returns wrapped Request data
     *
     * @param \Gateway\Request\AbstractRequest $request
     * @return array
     */
    public function getPostFields(AbstractRequest $request)
    {
        $payload = $request->getPayload();
        return [
            "payLoad" => $this->crypt->encrypt($payload),
            "apiKey" => $this->merchant->getId(),
            "lang" => $request->getLang()
        ];
    }

    /**
     * Returns concrete CryptInterface
     *
     * @return \Gateway\Crypt\CryptInterface
     */
    public function getCrypt()
    {
        return $this->crypt;
    }

    /**
     * Returns initialized cURL Resource
     *
     * @param \Gateway\Request\AbstractRequest $request
     * @return resource
     */
    private function getCurlInstance(AbstractRequest $request)
    {
        $curlInstance = curl_init($request->getEndpoint());
        $header = [
            "X-SDK-Version: " . GatewayConstants::VERSION
        ];
        curl_setopt($curlInstance, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlInstance, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curlInstance, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curlInstance, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curlInstance, CURLOPT_REFERER, $this->getOrigin());
        curl_setopt($curlInstance, CURLOPT_CUSTOMREQUEST, $request->getMethod());
        $this->crypt->curlOptions($curlInstance);
        $this->merchant->curlOptions($curlInstance);
        if ($request->hasAuth()) {
            $header = array_merge(
                $header,
                $this->getCurlAuthHeaders($request->getId(), $request->getAuthSignature())
            );
        }
        curl_setopt(
            $curlInstance,
            CURLOPT_HTTPHEADER,
            $header
        );
        return $curlInstance;
    }

    /**
     * Returns Auth Headers for cURL
     *
     * @param string $reqId
     * @param string $signature
     * @return array
     */
    private function getCurlAuthHeaders($reqId, $signature)
    {
        $curlHeader = [];
        foreach ($this->getAuthHeaders($reqId, $signature) as $key => $value) {
            $curlHeader[] = $key . ': ' . $value;
        }
        return $curlHeader;
    }

    /**
     * Returns Auth Headers
     *
     * @param mixed $reqId
     * @param mixed $reqSignature
     * @return array
     */
    public function getAuthHeaders($reqId, $reqSignature)
    {
        $istNow = new \DateTime("now", new \DateTimeZone('Asia/Kolkata'));
        $hashContent = implode('+', [
            $reqSignature,
            GatewayConstants::HMAC_SALT,
            $istNow->format('HisdmY'),
            $reqId
        ]);
        $hmachash = base64_encode(
            pack('H*', hash_hmac('sha256', $hashContent, $this->merchant->getApiToken()))
        );

        $hash = implode(':', [
            $this->merchant->getId(),
            $reqId,
            $hmachash
        ]);
        return [
            GatewayConstants::AUTHORIZATION => 'hmac ' . $hash,
            GatewayConstants::DATE_HEADER => $istNow->setTimezone(new \DateTimeZone('GMT'))->format('D, d M Y H:i:s e')
        ];
    }

    /**
     * Returns Origin
     *
     * @return string
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    private function getOrigin()
    {
        $fieldServerPort = 'SERVER_PORT';
        if (! isset($_SERVER["SERVER_NAME"])) {
            return 'http://127.0.0.1';
        }
        $currentURL = (@$_SERVER["HTTPS"] == "on") ? "https://" : "http://";
        $currentURL .= $_SERVER["SERVER_NAME"];
        if ($_SERVER[$fieldServerPort] != "80" && $_SERVER[$fieldServerPort] != "443") {
            $currentURL .= ":".$_SERVER[$fieldServerPort];
        }
        return $currentURL;
    }
}
