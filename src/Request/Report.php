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
use Gateway\Entities\ReportSetting;
use Gateway\Common\Fields;

/**
 * Report
 *
 * @see Gateway\Request\AbstractRequest
 */
class Report extends AbstractRequest
{
    /**
     * @inheritdoc
     */
    protected $auth = false;

    /**
     * @var string
     */
    protected $fromDate;

    /**
     * @var string
     */
    protected $toDate;

    /**
     * @var \Gateway\Entities\ReportSetting
     */
    protected $setting;

    /**
     * @inheritdoc
     */
    protected $required = [
        'merchant', 'fromDate', 'toDate', 'setting'
    ];

    /**
     * @param string $fromDate
     * @param \Gateway\Entities\ReportSetting $setting
     * @param string $toDate
     */
    public function __construct($fromDate, ReportSetting $setting = null, $toDate = null)
    {
        parent::__construct(null);
        $this->toDate = empty($toDate) ?
            date(\Gateway\Common\GatewayConstants::DATE_FORMAT) :
            date(\Gateway\Common\GatewayConstants::DATE_FORMAT, strtotime($toDate));
        $this->setting = ($setting instanceof ReportSetting) ? $setting : $this->setting = new ReportSetting();
        $this->fromDate = date(\Gateway\Common\GatewayConstants::DATE_FORMAT, strtotime($fromDate));
    }

    /**
     * @inheritdoc
     */
    public function getArray()
    {
        return array_merge(
            [
                Fields::MERCHANT => $this->merchant->getArray(),
                Fields::DATE_FROM => $this->fromDate,
                Fields::DATE_TO => $this->toDate
            ],
            $this->setting->getArray()
        );
    }

    /**
     * @inheritdoc
     */
    public function validate()
    {
        return (
            parent::validate()
            && strtotime($this->fromDate)
            && strtotime($this->toDate)
        );
    }
}
