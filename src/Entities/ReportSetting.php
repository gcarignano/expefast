<?php
/**
 * Copyright 2023 Connfido BV. All rights reserved.
 * Distribution of this software without explicit written consent from Newgen is
 * strictly prohibited. No part of this software must not be reverse engineered,
 * copied, reproduced or modified.
 */

namespace Gateway\Entities;

use DateTimeZone;
use Gateway\Common\GatewayConstants;
use Gateway\Common\Report;
use Gateway\Common\Columns;
use Gateway\Common\SortOrder;
use Gateway\Common\ConstantObject;
use Gateway\Common\Fields;
use Gateway\Utility\Validator;

/**
 * Report Setting
 *
 * @see Gateway\Entities\AbstractEntity
 * @final
 */
final class ReportSetting extends AbstractEntity
{
    /**
     * @var \Gateway\Common\Report
     */
    protected $report;

    /**
     * @var \Gateway\Common\SortOrder
     */
    protected $order;

    /**
     * @var \Gateway\Common\Columns
     */
    protected $sort;

    /**
     * @var \DateTimeZone
     */
    protected $timezone;

    /**
     * Index of a Report to start pagination from
     *
     * @var int
     */
    protected $fromCount;

    /**
     * Results per page in a Paginated Report
     *
     * @var int
     */
    protected $limitResult;

    /**
     * @var boolean
     */
    protected $showCustomData;

    /**
     * Column filters
     *
     * @var mixed
     */
    protected $filters = [];

    /**
     * @inheritdoc
     */
    protected $required = [
        "report", "order", "sort", "timezone"
    ];

    /**
     * @param Report $report
     * @param Columns $sortColumn
     * @param SortOrder $sortOrder
     * @param boolean $showCustomData
     * @param int $resultsPerPage
     * @param int $pageNo
     */
    public function __construct(
        Report $report = null,
        Columns $sortColumn = null,
        SortOrder $sortOrder = null,
        $showCustomData = false,
        $resultsPerPage = 0,
        $pageNo = 1
    ) {
        $this->report = is_null($report) ? Report::$TRANSACTION : $report;
        $this->order = is_null($sortOrder) ? SortOrder::$ASC : $sortOrder;
        $this->sort = is_null($sortColumn) ? Columns::$TXN_DATE : $sortColumn;
        $this->timezone = new \DateTimeZone('UTC');
        $this->showCustomData = filter_var($showCustomData, FILTER_VALIDATE_BOOLEAN);
        $this->paginate($resultsPerPage, $pageNo);
    }

    /**
     * Sets Timezone
     *
     * @param DateTimeZone $timezone
     * @return \Gateway\Entities\ReportSetting
     */
    public function setTimeZone(DateTimeZone $timezone)
    {
        $this->timezone = $timezone;
        return $this;
    }

    /**
     * Sets the pagination Report
     *
     * @param int $resultsPerPage
     * @param int $pageNo
     * @return \Gateway\Entities\ReportSetting
     */
    public function paginate($resultsPerPage, $pageNo = 1)
    {
        $pageNo = abs((int)$pageNo);
        $this->limitResult = abs((int)$resultsPerPage);
        $this->fromCount = ($pageNo ? $pageNo - 1 : 0) * $this->limitResult;
        return $this;
    }

    /**
     * Adds a Column Filter
     *
     * @param Columns $filterField
     * @param string $value
     * @return \Gateway\Entities\ReportSetting
     */
    public function addFilter(Columns $filterField, $value)
    {
        if ($filterField instanceof Columns) {
            $this->filters[$filterField->getValue()] = ($value instanceof ConstantObject) ? $value->getValue() : $value;
        }
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getArray()
    {
        $data = [
            Fields::LOCALE => (new \DateTime(null, $this->timezone))->format('T'),
            Fields::REPORTTYPE => $this->report->getValue(),
            Fields::SORTORDER => $this->order->getValue(),
            Fields::SORTCOLUMN => $this->sort->getValue(),
            Fields::SHOW_CUSTOM_DATA => $this->showCustomData
        ];
        foreach ($this->filters as $filterField => $filterValue) {
            $data[Fields::FILTER][] = [
                Fields::FILTER_FIELD => $filterField,
                Fields::FILTER_VALUE => $filterValue
            ];
        }
        if ($this->limitResult) {
            $data[Fields::LIMIT] = $this->limitResult;
        }
        if ($this->fromCount) {
            $data[Fields::FROM_COUNT] = $this->fromCount;
        }
        return $data;
    }

    /**
     * @inheritdoc
     */
    public function validate()
    {
        foreach ($this->filters as $filter) {
            Validator::isAllSpecial($filter);
        }
        return (
            parent::validate()
            && Validator::isNum($this->limitResult, 10, true)
            && Validator::isNum($this->fromCount, 10, true)
        );
    }
}
