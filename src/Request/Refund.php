<?php
/**
 * Copyright 2023 Connfido BV. All rights reserved.
 * Distribution of this software without explicit written consent from Newgen is
 * strictly prohibited. No part of this software must not be reverse engineered,
 * copied, reproduced or modified.
 */

namespace Gateway\Request;

use Gateway\Common\Fields;
use Gateway\Entities\DynamicDescriptor;
use Gateway\HttpClient\GatewayResponse;
use Gateway\Utility\Validator;

/**
 * Refund
 *
 * @see Gateway\Request\AbstractRequest
 */
class Refund extends AbstractRequest
{
    /**
     * @var string
     */
    protected $amount;

    /**
     * @var string
     */
    protected $invoiceNo;

    /**
     * @var string
     */
    protected $comments;

    /**
     * @var \Gateway\Entities\DynamicDescriptor
     */
    protected $dd;

    /**
     * @inheritdoc
     */
    protected $authSignature = \Gateway\Common\GatewayConstants::API_TYPE_REFUND;

    /**
     * @inheritdoc
     */
    protected $required = [
        'id', 'merchant'
    ];

    /**
     * @param string $txnId
     * @param string $amount
     */
    public function __construct($txnId, $amount)
    {
        parent::__construct($txnId);
        $this->amount = $amount;
    }

    /**
     * Sets Invoice Number
     *
     * @param string $param
     * @return \Gateway\Request\Refund
     */
    public function setInvoiceNo($param)
    {
        $this->invoiceNo = $param;
        return $this;
    }

    /**
     * Sets Comment
     *
     * @param string $param
     * @return \Gateway\Request\Refund
     */
    public function setComment($param)
    {
        $this->comment = $param;
        return $this;
    }

    /**
     * Set DynamicDescriptor
     *
     * @param \Gateway\Entities\DynamicDescriptor $dd
     * @return \Gateway\Request\Refund
     */
    public function setDynamicDescriptor(DynamicDescriptor $dd)
    {
        $this->dd = $dd;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getArray()
    {
        $data = $this->merchant->getArray();
        $data[Fields::REFUND] = [
            Fields::REFUND_AMOUNT => $this->amount,
            Fields::TXN_REFERENCE => $this->getId(),
        ];
        if (! empty($this->comment)) {
            $data[Fields::REFUND][Fields::COMMENTS] = $this->comment;
        }
        if (! empty($this->invoiceNo)) {
            $data[Fields::REFUND][Fields::REFUND_INVOICE] = $this->invoiceNo;
        }
        if ($this->dd) {
            $data = array_merge($data, $this->dd->getArray());
        }
        return $data;
    }

    /**
     * @inheritdoc
     */
    public function validate()
    {
        if ($this->dd instanceof DynamicDescriptor) {
            Validator::isValidEntity($this->dd);
        }
        return (
            parent::validate()
            && Validator::isAmount($this->amount)
            && Validator::isAlnumSpecial($this->getId(), 100)
            && Validator::isAlnumSpecial($this->invoiceNo, 100, true)
            && Validator::isAllSpecial($this->comment, 100, true)
        );
    }

    /**
     * @inheritdoc
     */
    public function process(GatewayResponse $response)
    {
        $res = parent::process($response);
        if (($data = $res->getData()) && in_array($data['response']['responseCode'], [200,100])) {
            $res->setData($data['response']);
            $this->completed = true;
        }
        return $this->completed ? $res : $res->badResponse();
    }
}
