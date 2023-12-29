<?php
/**
 * Copyright 2023 Connfido BV. All rights reserved.
 * Distribution of this software without explicit written consent from Newgen is
 * strictly prohibited. No part of this software must not be reverse engineered,
 * copied, reproduced or modified.
 */

namespace Gateway\Entities;

use Gateway\Utility\Validator;
use Gateway\Exceptions\ValidationException;
use Gateway\Common\Fields;
use Gateway\Common\Messages;

/**
 * Order Items
 *
 * @see Gateway\Entities\AbstractEntity
 * @final
 */
final class Items extends AbstractEntity
{
    /**
     * @var array
     */
    protected $data = [];

    /**
     * @inheritdoc
     */
    protected $required = [
        "data"
    ];

    /**
     * @param string $name
     * @param string $price
     * @param int $qty
     * @param string $sku
     */
    public function __construct($name, $price, $qty = 1, $sku = null)
    {
        $this->addItem($name, $price, $qty, $sku);
    }

    /**
     * @inheritdoc
     */
    public function getArray()
    {
        return $this->data;
    }

    /**
     * @inheritdoc
     */
    public function validate()
    {
        foreach ($this->data as $itemData) {
            if (!Validator::isItemAmount($itemData[Fields::ITEM_PRICE])
                || !Validator::isNum($itemData[Fields::ITEM_QTY], 6)
                || !Validator::isAllSpecial($itemData[Fields::ITEM_NAME])
                || !Validator::isAllSpecial(@$itemData[Fields::ITEM_SKU], 100, true)
            ) {
                throw new ValidationException(Messages::ITEM_DETAIL);
            }
        }
        return true;
    }

    /**
     * Add an Order Item
     *
     * @param string $name
     * @param string $price
     * @param int $qty
     * @param string $sku
     * @return \Gateway\Entities\Items
     */
    public function addItem($name, $price, $qty = 1, $sku = null)
    {
        $dataItem = [
            Fields::ITEM_NAME => $name,
            Fields::ITEM_PRICE => $price,
            Fields::ITEM_QTY => $qty
        ];
        if (! empty($sku)) {
            $dataItem[Fields::ITEM_SKU] = $sku;
        }
        $this->data[] = $dataItem;
        return $this;
    }
}
