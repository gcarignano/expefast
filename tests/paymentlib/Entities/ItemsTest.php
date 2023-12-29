<?php

namespace GatewayTests\Entities;

use \Gateway\Common\Fields;
use \Gateway\Entities\Items;

class ItemsTest extends \GatewayTests\TestCase
{
    public function testConstructor()
    {
        $items = new Items("product 01", 100.95);
        $this->assertTrue($items->validate());

        $arr = $items->getArray();
        $this->assertEquals(1, count($arr));
        $this->assertEquals("product 01", $arr[0][Fields::ITEM_NAME]);
        $this->assertEquals(100.95, $arr[0][Fields::ITEM_PRICE]);
        $this->assertEquals(1, $arr[0][Fields::ITEM_QTY]);
        $this->assertFalse(isset($arr[0][Fields::ITEM_SKU]));
    }

    public function testMultipleItems()
    {
        $items = (new Items("product 01", 100.95))->addItem("product 02", 95.10, 100, 'skuprod2');
        $this->assertTrue($items->validate());

        $arr = $items->getArray();
        $this->assertEquals(2, count($arr));
        $this->assertEquals("product 02", $arr[1][Fields::ITEM_NAME]);
        $this->assertEquals(95.10, $arr[1][Fields::ITEM_PRICE]);
        $this->assertEquals(100, $arr[1][Fields::ITEM_QTY]);
        $this->assertEquals("skuprod2", $arr[1][Fields::ITEM_SKU]);
    }
}
