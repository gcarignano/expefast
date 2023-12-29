<?php
/**
 * Copyright 2023 Connfido BV. All rights reserved.
 * Distribution of this software without explicit written consent from Newgen is
 * strictly prohibited. No part of this software must not be reverse engineered,
 * copied, reproduced or modified.
 */

namespace Gateway\Request;

use Gateway\HttpClient\GatewayResponse;
use Gateway\Response;
use Gateway\Common\Fields;
use Gateway\Common\Messages;
use Gateway\Entities\CompanyDetails as Details;

/**
 * Company Details
 *
 * @see Gateway\Request\AbstractRequest
 */
class CompanyDetails extends AbstractRequest
{
    /**
     * @inheritdoc
     */
    protected $auth = false;

    /**
     * @var \Gateway\Entities\CompanyDetails
     */
    protected $details;

    /**
     * @inheritdoc
     */
    protected $required = [
        'merchant', 'details'
    ];

    /**
     * @param \Gateway\Entities\CompanyDetails $details
     */
    public function __construct(Details $details)
    {
        parent::__construct(null);
        $this->details = $details;
    }

    /**
     * @inheritdoc
     */
    public function getArray()
    {
        return array_merge(
            $this->merchant->getArray(),
            $this->details->getArray()
        );
    }

    /**
     * @inheritdoc
     */
    public function process(GatewayResponse $response)
    {
        $res = parent::process($response);
        $data = $res->getData();
        if (isset($data['regNo'])) {
            $this->completed = true;
        }
        return $this->completed ? $res : $res->badResponse(Messages::COMPANY);
    }
}
