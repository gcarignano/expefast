<?php
/**
 * Copyright 2023 Connfido BV. All rights reserved.
 * Distribution of this software without explicit written consent from Newgen is
 * strictly prohibited. No part of this software must not be reverse engineered,
 * copied, reproduced or modified.
 */

namespace Gateway\Entities;

use Gateway\Common\Fields;

final class WireTransferInfo extends AbstractEntity implements WhppInfoInterface
{
    protected $required = [
        "id"
    ];

    public function __construct($registrationId)
    {
        parent::__construct($registrationId);
    }

    /**
     * @inheritdoc
     */
    public function getArray()
    {
        return [
            Fields::REGISTRATIONID => $this->getId(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function validate()
    {
        return parent::validate();
    }
}
