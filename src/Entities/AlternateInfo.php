<?php

/**
 * Copyright 2021 Newgen Payment Gateway Pvt. Ltd. All rights reserved.
 * Distribution of this software without explicit written consent from Newgen is
 * strictly prohibited. No part of this software must not be reverse engineered,
 * copied, reproduced or modified.
 */

namespace Gateway\Entities;

use Gateway\Common\Fields;

final class AlternateInfo extends AbstractEntity implements WhppInfoInterface
{
    private $documentType;

    protected $required = [
        "id"
    ];

    public function __construct($documentId, $documentType)
    {
        parent::__construct($documentId);
        $this->documentType = $documentType;
    }

    /**
     * @inheritdoc
     */
    public function getArray()
    {
        return [
            Fields::DOCUMENT_ID => $this->getId(),
            Fields::DOCUMENT_TYPE => $this->documentType,
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
