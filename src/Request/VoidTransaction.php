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
 * Void Transaction
 *
 * @see Gateway\Request\AbstractRequest
 */
class VoidTransaction extends AbstractRequest
{
    /**
     * @inheritdoc
     */
    protected $authSignature = \Gateway\Common\GatewayConstants::API_TYPE_VOID_PAYMENT;

    /**
     * @var \Gateway\Entities\DynamicDescriptor
     */
    protected $dd;

    /**
     * @inheritdoc
     */
    protected $required = [
        'id', 'merchant'
    ];

    /**
     * @param string $txnId
     * @param \Gateway\Entities\DynamicDescriptor $dd
     */
    public function __construct($txnId, DynamicDescriptor $dd = null)
    {
        parent::__construct($txnId);
        $this->dd = $dd;
    }

    /**
     * @inheritdoc
     */
    public function getArray()
    {
        return array_merge(
            $this->merchant->getArray(),
            [
                Fields::TXN_VOID => [
                    Fields::TXN_REFERENCE => $this->getId()
                ]
            ],
            ($this->dd ? $this->dd->getArray() : [])
        );
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
        if (isset($data['response']['responseCode'])
            && $data['response']['responseCode'] == 200
        ) {
            $res->setData($data['response']);
            $this->completed = true;
        }
        return $this->completed ? $res : $res->badResponse();
    }
}
