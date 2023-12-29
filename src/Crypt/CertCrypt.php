<?php
/**
 * Copyright 2023 Connfido BV. All rights reserved.
 * Distribution of this software without explicit written consent from Newgen is
 * strictly prohibited. No part of this software must not be reverse engineered,
 * copied, reproduced or modified.
 */

namespace Gateway\Crypt;

use Gateway\Utility\Validator;
use Gateway\Exceptions\BadObject;
use Gateway\Common\Messages;

/**
 * Certificate based encryption with HMAC
 *
 * @see Gateway\Crypt\CryptInterface
 * @final
 */
final class CertCrypt implements CryptInterface
{
    const ENCRYPTION = 'AES-256-CBC';

    private $apiKey;

    private $apiToken;

    private $certFile;

    private $privateKeyFile;

    private $fingerprint;

    private $encryptKey;

    private $decryptKey;

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function __construct()
    {
        $merchant = \Gateway\App::getMerchant();
        $this->apiKey = $merchant->getId();
        $this->apiToken = $merchant->getApiToken();
        $this->certFile = $merchant->getCertFile();
        $this->privateKeyFile = $merchant->getPrivateKeyFile();
        $this->init();
    }

    /**
     * Initialized encryption and decryption key
     */
    private function init()
    {
        $certContent = file_get_contents($this->certFile);
        $this->fingerprint = implode(
            ':',
            str_split(strtoupper(openssl_x509_fingerprint($certContent, 'sha1')), 2)
        );
        $this->encryptKey = substr(($this->apiToken | $this->fingerprint), 0, strlen($this->apiToken));
        $this->decryptKey = substr($this->fingerprint, 0, 32);
    }

    /**
     * @inheritdoc
     */
    public function curlOptions(&$curlInstance)
    {
        if (! Validator::isCurl($curlInstance)) {
            throw new BadObject(Messages::CURL);
        }
        curl_setopt($curlInstance, CURLOPT_SSLCERT, $this->certFile);
        curl_setopt($curlInstance, CURLOPT_SSLKEY, $this->privateKeyFile);
    }

    /**
     * @inheritdoc
     */
    public function encrypt($data)
    {
        $initVector = openssl_random_pseudo_bytes(openssl_cipher_iv_length(self::ENCRYPTION));
        return bin2hex($initVector . openssl_encrypt(
            $data,
            self::ENCRYPTION,
            $this->encryptKey,
            OPENSSL_RAW_DATA,
            $initVector
        ));
    }

    /**
     * @inheritdoc
     */
    public function decrypt($data)
    {
        $binaryData = hex2bin($data);
        $ivSize = openssl_cipher_iv_length(self::ENCRYPTION);
        $initVector = substr($binaryData, 0, $ivSize);
        return rtrim(openssl_decrypt(
            substr($binaryData, $ivSize),
            self::ENCRYPTION,
            $this->decryptKey,
            OPENSSL_RAW_DATA | OPENSSL_NO_PADDING,
            $initVector
        ));
    }
}
