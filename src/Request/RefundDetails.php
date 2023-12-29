<?php
/**
 * Copyright 2023 Connfido BV. All rights reserved.
 * Distribution of this software without explicit written consent from Newgen is
 * strictly prohibited. No part of this software must not be reverse engineered,
 * copied, reproduced or modified.
 */

namespace Gateway\Request;

use Gateway\HttpClient\GatewayResponse;
use Gateway\Utility\Validator;
use Gateway\Common\Fields;

/**
 * Refund Details
 *
 * @see Gateway\Request\AbstractRequest
 */
class RefundDetails extends AbstractRequest
{
    /**
     * @inheritdoc
     */
    protected $authSignature = \Gateway\Common\GatewayConstants::API_TYPE_REFUND_STATUS;

    /**
     * @inheritdoc
     */
    protected $required = [
        'id', 'merchant'
    ];

    /**
     * @inheritdoc
     */
    public function getArray()
    {
        return array_merge(
            $this->merchant->getArray(),
            [
                Fields::REFUND_STATUS => [
                    Fields::TXN_REFERENCE => $this->getId()
                ]
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function validate()
    {
        return (
            parent::validate()
            && Validator::isAlnumSpecial($this->getId(), 100)
        );
    }

    /**
     * @inheritdoc
     */
    public function process(GatewayResponse $response)
    {
        $res = parent::process($response);
        if (($data = $res->getData()) && isset($data['totalTxnAmount'])) {
            $this->completed = true;
        }
        return $this->completed ? $res : $res->badResponse();
    }
}
