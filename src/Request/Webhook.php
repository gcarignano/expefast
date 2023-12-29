<?php
/**
 * Copyright 2023 Connfido BV. All rights reserved.
 * Distribution of this software without explicit written consent from Newgen is
 * strictly prohibited. No part of this software must not be reverse engineered,
 * copied, reproduced or modified.
 */

namespace Gateway\Request;

use Gateway\Common\Events;
use Gateway\Common\Fields;
use Gateway\Common\Messages;
use Gateway\Common\GatewayConstants;
use Gateway\Exceptions\InvalidArgument;
use Gateway\Exceptions\ValidationException;
use Gateway\HttpClient\GatewayResponse;
use Gateway\Response;
use Gateway\Utility\Validator;

/**
 * Webhook
 *
 * @see Gateway\Request\AbstractRequest
 */
class Webhook extends AbstractRequest
{
    const DEACTIVATE = 1;

    const DETAILS = 2;

    const CREATE = 3;

    const UPDATE = 4;

    /**
     * Webhook url
     *
     * @var string
     */
    protected $url;

    /**
     * Array of Gateway\Common\Events
     *
     * @var mixed
     */
    protected $event;

    /**
     * Type of operation
     *
     * @var int
     */
    protected $operation = self::DETAILS;

    /**
     * @inheritdoc
     */
    protected $auth = false;

    /**
     * @inheritdoc
     */
    protected $required = [
        'merchant',
        'id'
    ];

    /**
     * @param \Gateway\Entities\Customer|string $customer
     * @param \Gateway\Common\PaymentMode $paymentMode
     * @param \Gateway\Common\CardTypes $cardType
     * @param string $tokenId
     */
    public function __construct($webhookId = null, $url = null, Events ...$event)
    {
        parent::__construct($webhookId);
        $this->url = $url;
        $this->event = $event;
        if (empty($event)) {
            $this->event = Events::$all;
        }
    }

    /**
     * @inheritdoc
     */
    public function getArray()
    {
        $data = $this->merchant->getArray();
        $data[Fields::WEBHOOK] = [];
        if ($this->operation != self::CREATE) {
            $data[Fields::WEBHOOK][Fields::WEBHOOK_ID] = $this->getId();
        }
        if (in_array($this->operation, [self::CREATE, self::UPDATE])) {
            $events = [];
            foreach ($this->event as $e) {
                $events[] = $e->getValue();
            }
            $data[Fields::WEBHOOK][Fields::EVENTS] = implode(',', $events);
            $data[Fields::WEBHOOK][Fields::URL] = $this->url;
        }
        if ($this->operation == self::DEACTIVATE) {
            $data[Fields::WEBHOOK][Fields::STATUS] = "inactive";
        }
        return $data;
    }

    /**
     * Deactivate webhook
     *
     * @return \Gateway\Request\Webhook
     */
    public function remove()
    {
        $this->operation = self::DEACTIVATE;
        $this->required = [
            'merchant',
            'id'
        ];
        return $this->send();
    }

    /**
     * Webhook status and details
     *
     * @return \Gateway\Request\Webhook
     */
    public function details()
    {
        $this->operation = self::DETAILS;
        $this->required = [
            'merchant',
            'id'
        ];
        return $this->send();
    }

    /**
     * Create webhook for specific events
     *
     * @return \Gateway\Request\Webhook
     */
    public function create()
    {
        $this->operation = self::CREATE;
        $this->required = [
            'merchant',
            'url',
            'event'
        ];
        return $this->send();
    }

    /**
     * Update webhook for specific events
     *
     * @return \Gateway\Request\Webhook
     */
    public function update()
    {
        $this->operation = self::UPDATE;
        $this->required = [
            'merchant',
            'id',
            'url',
            'event'
        ];
        return $this->send();
    }

    /**
     * @inheritdoc
     */
    public function process(GatewayResponse $response)
    {
        $res = parent::process($response);
        $data = $res->getData();
        if (!empty($data) && !isset($data['description'])) {
            $this->completed = true;
        }
        return $this->completed ? $res : $res->badResponse();
    }

    /**
     * Parse data from webhook body
     *
     * @param mixed $data
     * @param mixed $origin
     * @return string
     */
    public function parseData($data, $origin)
    {
        if (strlen($data) % 2 != 0 || !ctype_xdigit($data)) {
            throw new InvalidArgument(Messages::WEBHOOK_DATA);
        }
        $data = utf8_decode($this->request->getCrypt()->decrypt($data));
        $data = preg_replace('/[[:cntrl:]]/', '', $data);
        return $this->process(new GatewayResponse($data));
    }
}
