<?php
/**
 * Copyright 2023 Connfido BV. All rights reserved.
 * Distribution of this software without explicit written consent from Newgen is
 * strictly prohibited. No part of this software must not be reverse engineered,
 * copied, reproduced or modified.
 */

namespace Gateway\Request;

use Gateway\Common\Fields;
use Gateway\HttpClient\GatewayResponse;
use Gateway\Utility\Validator;

/**
 * Approve/Decline WireTransfer Transaction
 *
 * @see Gateway\Request\AbstractRequest
 */
class WireTransferBankTransaction extends AbstractRequest
{
    /**
     * Approve Wiretransfer transaction
     */
    const APPROVE = 1;

    /**
     *  Decline Wiretansfer transsaction
     */
    const DECLINE = 2;

    /**
     * @inheritDoc
     */
    protected $auth = false;


    /**
     * @inheritDoc
     */
    protected $operation = self::APPROVE;

    /**
     * @inheritdoc
     */
    protected $required = [
        'id'
    ];

    /**
     * @param string $txnId
     * @param string $amount
     */
    public function __construct($txnId, $amount = null)
    {
        parent::__construct($txnId);
        $this->amount = $amount;
    }

    /**
     * @inheritdoc
     */
    public function getMethod()
    {
        $method = [
            self::APPROVE => "POST",
            self::DECLINE => "DELETE"
        ];
        return $method[$this->operation];
    }

    /**
     * Decline WireTransfer transaction
     *
     * @return \Gateway\Request\WireTransferBankTransaction
     */
    public function decline()
    {
        $this->operation = self::DECLINE;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getEndpoint()
    {
        $suffix = '/' . $this->getId();
        return $this->merchant->getApiKeyEndpoint() . '/wire-transfer/transaction' . $suffix;
    }

    /**
     * @inheritdoc
     */
    public function getArray()
    {
        $data = [
            Fields::TXN_REFERENCE => $this->getId()
        ];
        if ($this->amount != null) {
            $data[Fields::TXN_AMOUNT] = $this->amount;
        }
        return $data;
    }

    /**
     * @inheritdoc
     */
    public function validate()
    {
        return parent::validate()
            && Validator::isAlnumSpecial($this->getId(), 100)
            && Validator::isAmount($this->amount, false, true);
    }

    /**
     * @inheritdoc
     */
    public function process(GatewayResponse $response)
    {
        $res = parent::process($response);
        if (($data = $res->getData()) && @$data['response']['responseCode'] != 200) {
            $res->badResponse();
        }
        return $res;
    }
}
