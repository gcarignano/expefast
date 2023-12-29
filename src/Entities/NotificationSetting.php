<?php
/**
 * Copyright 2023 Connfido BV. All rights reserved.
 * Distribution of this software without explicit written consent from Newgen is
 * strictly prohibited. No part of this software must not be reverse engineered,
 * copied, reproduced or modified.
 */

namespace Gateway\Entities;

use Gateway\Common\Fields;
use Gateway\Common\NotificationChannel;
use Gateway\Utility\Validator;

/**
 * Notification Setting
 *
 * @see Gateway\Entities\AbstractEntity
 * @final
 */
final class NotificationSetting extends AbstractEntity
{
    /**
     * @var mixed
     */
    protected $channel = [];

    /**
     * @param NotificationChannel $channel
     * @param string $value
     */
    public function __construct(NotificationChannel $channel = null, $value = null)
    {
        if ($channel != null) {
            $this->addChannel($channel, $value);
        }
    }

    /**
     * @inheritdoc
     */
    public function getArray()
    {
        return $this->channel;
    }

    /**
     * @inheritdoc
     */
    public function validate()
    {
        foreach ($this->channel as $chan) {
            Validator::isUniAlnumSpecial($chan[Fields::CHANNEL_VALUE], 255);
        }
        return parent::validate();
    }

    /**
     * Add notificaiton channel
     *
     * @param NotificationChannel $channel
     * @param string $value
     * @return \Gateway\Entities\NotificationSetting
     */
    public function addChannel(NotificationChannel $channel, $value)
    {
        $this->channel[] = [
            Fields::CHANNEL_NAME => $channel->getValue(),
            Fields::CHANNEL_VALUE => $value
        ];
        return $this;
    }
}
