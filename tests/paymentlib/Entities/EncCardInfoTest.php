<?php

namespace GatewayTests\Entities;

use \Gateway\Entities\EncCardInfo;
use \Gateway\Common\Fields;
use \Gateway\Common\CardTypes;

class EncCardInfoTest extends \GatewayTests\TestCase
{

    public function testConstructor()
    {
        $card = new EncCardInfo(
            "5d0e5b97b513780c4abf1d05937b288e",
            "John Smith",
            "2019",
            "12",
            "worldline",
            "dukpt2009",
            "1044201196027ec0000a"
        );
        $this->assertTrue($card->validate());
        foreach ([
            Fields::CARD_ENC_NUMBER => '5d0e5b97b513780c4abf1d05937b288e',
            Fields::CARD_HOLDER => 'John Smith',
            Fields::CARD_YEAR => '2019',
            Fields::CARD_MONTH => '12',
            Fields::ACQUIRER => 'worldline',
            Fields::CARD_ENC_ALGO => 'dukpt2009',
            Fields::CARD_ENC_KEYSEQ => '1044201196027ec0000a',
        ] as $k => $v) {
            $this->assertEquals($v, $card->getArray()[$k], "Failed for: $k");
        }
    }

    public function testSetCardType()
    {
        $card = new EncCardInfo(
            "5d0e5b97b513780c4abf1d05937b288e",
            "John Smith",
            "2019",
            "12",
            "worldline",
            "dukpt2009",
            "1044201196027ec0000a"
        );
        $card->setCardType(CardTypes::$JCB);
        $this->assertTrue($card->validate());
        $this->assertEquals(CardTypes::$JCB->getValue(), $card->getArray()[Fields::CARDTYPE]);
    }
}
