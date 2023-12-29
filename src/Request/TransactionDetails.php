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
use Gateway\Response;
use Gateway\Common\Fields;

/**
 * Transaction Details
 *
 * @see Gateway\Request\AbstractRequest
 */
class TransactionDetails extends AbstractRequest
{
    /**
     * @var \Gateway\Entities\Merchant
     */
    protected $merchant;

    /**
     * @var boolean
     */
    protected $showCustomData;

    /**
     * @inheritdoc
     */
    protected $authSignature = \Gateway\Common\GatewayConstants::API_TYPE_TXN_STATUS;

    /**
     * @inheritdoc
     */
    protected $required = [
        'id', 'merchant'
    ];

    /**
     * @param string $txnId
     * @param boolean $showCustomData
     */
    public function __construct($txnId, $showCustomData = false)
    {
        parent::__construct($txnId);
        $this->showCustomData = filter_var($showCustomData, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * @inheritdoc
     */
    public function getArray()
    {
        return array_merge(
            $this->merchant->getArray(),
            [
                Fields::TXN_REFERENCE => $this->getId(),
                Fields::SHOW_CUSTOM_DATA => $this->showCustomData
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
        $data = $res->getData();
        if (isset($data['txnReference'])) {
            $this->completed = true;
        }
        return $this->completed ? $res : $res->badResponse();
    }
}
