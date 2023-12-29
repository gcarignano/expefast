<?php
/**
 * Copyright 2023 Connfido BV. All rights reserved.
 * Distribution of this software without explicit written consent from Newgen is
 * strictly prohibited. No part of this software must not be reverse engineered,
 * copied, reproduced or modified.
 */

namespace Gateway\Entities;

use Gateway\Common\HostedFieldColumns;
use Gateway\Common\Messages;
use Gateway\Exceptions\ValidationException;

/**
 * Hosted Fields Setting
 *
 * @see Gateway\Entities\AbstractEntity
 * @final
 */
final class HostedFieldsSetting extends AbstractEntity
{
    /**
     * @var mixed
     */
    protected $columns = [];

    /**
     * @param HostedFieldColumns $column
     */
    public function __construct(HostedFieldColumns $column = null)
    {
        if ($column != null) {
            $this->addColumn($column);
        }
    }

    /**
     * @inheritdoc
     */
    public function getArray()
    {
        return $this->columns;
    }

    /**
     * @inheritdoc
     */
    public function validate()
    {
        if( count($this->columns) === 0 ) {
            throw new ValidationException(Messages::INVALID_HOSTED_FIELD_COLUMNS);
        }
        return parent::validate();
    }

    /**
     * Add Hosted Field Column
     *
     * @param HostedFieldColumns $column
     * @return \Gateway\Entities\HostedFieldsSetting
     */
    public function addColumn(HostedFieldColumns $column)
    {
        $this->columns[] = $column->getValue();
        return $this;
    }
}
