<?php
/**
 * Copyright 2023 Connfido BV. All rights reserved.
 * Distribution of this software without explicit written consent from Newgen is
 * strictly prohibited. No part of this software must not be reverse engineered,
 * copied, reproduced or modified.
 */

namespace Gateway\Request;

use Gateway\HttpClient\GatewayResponse;
use Gateway\Entities\Customer;
use Gateway\Common\Fields;
use Gateway\Entities\HostedFieldsSetting;

/**
 * Tokens
 *
 * @see Gateway\Request\AbstractRequest
 */
class HostedFields extends AbstractRequest
{
    /**
     * @var \Gateway\Entities\Customer
     */
    protected $customer;

    /**
     * @inheritdoc
     */
    protected $columns;

    /**
     * @inheritdoc
     */
    protected $required = [
        'merchant'
    ];

    /**
     * @param \Gateway\Entities\Customer|string $customer
     * @param \Gateway\Entities\HostedFieldsSetting $columns
     * @param string $tokenId
     */
    public function __construct($customer, HostedFieldsSetting $columns)
    {
        parent::__construct(null);
        $this->customer = ($customer instanceof Customer) ? $customer : new Customer($customer);
        $this->customer->setIdRequired();
        $this->columns = $columns;
    }

    /**
     * @inheritdoc
     */
    public function getArray()
    {
        return array_merge(
            $this->merchant->getArray(),
            $this->customer->getCustomerIdField(),
            [
                Fields::HOSTED_FIELD_COLUMNS => $this->columns->getArray()
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
            && $this->customer->validate()
            && $this->columns->validate()
        );
    }

    /**
     * @inheritdoc
     */
    public function process(GatewayResponse $response)
    {
        $res = parent::process($response);
        $data = $res->getData();
        if (
            isset($data['url'])
            && isset($data['requestID'])
        ) {
            $this->completed = true;
        }
        return $this->completed ? $res : $res->badResponse();
    }
}
