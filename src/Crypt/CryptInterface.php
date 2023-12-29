<?php
/**
 * Copyright 2023 Connfido BV. All rights reserved.
 * Distribution of this software without explicit written consent from Newgen is
 * strictly prohibited. No part of this software must not be reverse engineered,
 * copied, reproduced or modified.
 */

namespace Gateway\Crypt;

/**
 * CryptInterface
 */
interface CryptInterface
{
    /**
     * Encrypts data
     *
     * @param string $data
     * @return string
     */
    public function encrypt($data);

    /**
     * Decrypts data
     *
     * @param string $data
     * @return string
     */
    public function decrypt($data);

    /**
     * Initializes curl resource with cryptographic information
     *
     * @param resource $curlInstance
     */
    public function curlOptions(&$curlInstance);
}
