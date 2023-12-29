<?php
/**
 * Copyright 2023 Connfido BV. All rights reserved.
 * Distribution of this software without explicit written consent from Newgen is
 * strictly prohibited. No part of this software must not be reverse engineered,
 * copied, reproduced or modified.
 */

namespace Gateway\Entities;

use Gateway\Common\Fields;
use Gateway\Common\Messages;
use Gateway\Exceptions\ValidationException;
use Gateway\Utility\Validator;

/**
 * Custom Transaction Data
 *
 * @see Gateway\Entities\AbstractEntity
 * @final
 */
final class CustomData extends AbstractEntity
{
    /**
     * @var string
     */
    protected $customData1;

    /**
     * @var string
     */
    protected $customData2;

    /**
     * @var string
     */
    protected $customData3;

    /**
     * @var string
     */
    protected $customData4;

    /**
     * @var string
     */
    protected $customData5;

    /**
     * @var string
     */
    protected $site;

    /**
     * @param string $customData1
     * @param string $customData2
     * @param string $customData3
     * @param string $customData4
     * @param string $customData5
     */
    public function __construct(
        $customData1,
        $customData2 = '',
        $customData3 = '',
        $customData4 = '',
        $customData5 = '',
        $site = ''
    ) {
        $this->customData1 = $customData1;
        $this->customData2 = $customData2;
        $this->customData3 = $customData3;
        $this->customData4 = $customData4;
        $this->customData5 = $customData5;
        $this->site = $site;
    }

    /**
     * Set site
     *
     * @param string $site
     * @return \Gateway\Entities\CustomData
     */
    public function setSite($site)
    {
        $this->site = $site;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getArray()
    {
        return [
            Fields::CUSTOM_DATA1 => $this->customData1,
            Fields::CUSTOM_DATA2 => $this->customData2,
            Fields::CUSTOM_DATA3 => $this->customData3,
            Fields::CUSTOM_DATA4 => $this->customData4,
            Fields::CUSTOM_DATA5 => $this->customData5,
            Fields::SITE => $this->site
        ];
    }

    /**
     * @inheritdoc
     */
    public function validate()
    {
        return (parent::validate()
            && Validator::isUniAlnumSpecial($this->customData1, 255)
            && Validator::isUniAlnumSpecial($this->customData2, 255, true)
            && Validator::isUniAlnumSpecial($this->customData3, 255, true)
            && Validator::isUniAlnumSpecial($this->customData4, 255, true)
            && Validator::isUniAlnumSpecial($this->customData5, 255, true)
            && Validator::isUniAlnumSpecial($this->site, 255, true)
        );
    }
}
