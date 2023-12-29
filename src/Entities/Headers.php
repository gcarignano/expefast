<?php
/**
 * Copyright 2023 Connfido BV. All rights reserved.
 * Distribution of this software without explicit written consent from Newgen is
 * strictly prohibited. No part of this software must not be reverse engineered,
 * copied, reproduced or modified.
 */

namespace Gateway\Entities;

use Gateway\Utility\Validator;
use Gateway\Common\Fields;

/**
 * Bank Information for WHPP
 *
 * @see Gateway\Entities\WhppInfoInterface
 * @see Gateway\Entities\AbstractEntity
 * @final
 */
final class Headers extends AbstractEntity
{

    /**
     * Client IP
     *
     * @var string
     */
    private $clientIP;

    /**
     * @param string $clientIP
     */
    public function __construct($clientIP = null)
    {
        $this->clientIP = $clientIP;
    }

    /**
     * Set X-Client-IP Header
     *
     * @param string $value
     * @return \Gateway\Entities\Headers
     */
    public function setClientIP($clientIP)
    {
        $this->clientIP = $clientIP;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getArray()
    {
        $data = [];
        if ($this->clientIP) {
            $data = [
                Fields::X_CLIENT_IP . ': ' . $this->clientIP
            ];
        }
        return $data;
    }

    /**
     * @inheritdoc
     */
    public function validate()
    {
        return (parent::validate()
            && Validator::isIPAddress($this->clientIP, true)
        );
    }
}
