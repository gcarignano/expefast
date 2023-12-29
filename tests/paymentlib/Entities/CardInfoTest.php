<?php

namespace GatewayTests\Entities;

use \Gateway\Entities\CardInfo;
use \Gateway\Common\Fields;
use \Gateway\Common\CardTypes;

class CardInfoTest extends \GatewayTests\TestCase
{

    private function newCard()
    {
        return new CardInfo(
            '679028738917',
            'John Doe',
            '111',
            '2022',
            '03',
            true
        );
    }

    public function testConstructor()
    {
        $card = $this->newCard();
        $this->assertTrue($card->validate());
        foreach ([
            Fields::CARD_NUMBER => '679028738917',
            Fields::CARD_HOLDER => 'John Doe',
            Fields::CARD_CVV => '111',
            Fields::CARD_YEAR => '2022',
            Fields::CARD_MONTH => '03'
        ] as $k => $v) {
            $this->assertEquals($v, $card->getArray()[$k], "Failed for: $k");
        }
        $this->assertTrue($card->getArray()[Fields::CARD_SAVE]);
    }

    public function testCardNumberExceedingMaxLength()
    {
        $card = new CardInfo('898982918298192819280129102810928091820182981082120182', 'holder name');
        $this->assertFalse($card->validate());
    }

    public function testCardCVVExceedingMaxLength()
    {
        $card = new CardInfo('123123123', 'holder name', '12345');
        $this->assertFalse($card->validate());
    }

    public function testCardTokens()
    {
        $card = new CardInfo('679028738917');
        $this->assertTrue($card->validate());
        $this->assertTrue(isset($card->getArray()['tokenID']));
    }

    public function testSetCardType()
    {
        $card = $this->newCard();
        $card->setCardType(CardTypes::$JCB);
        $this->assertTrue($card->validate());
        $this->assertEquals(CardTypes::$JCB->getValue(), $card->getArray()[Fields::CARDTYPE]);
    }

    public function testSetAcquirer()
    {
        $card = $this->newCard();
        $card->setAcquirer('acq');
        $this->assertTrue($card->validate());
        $this->assertEquals('acq', $card->getArray()[Fields::ACQUIRER]);
    }

    public function testSetAcquirerToken()
    {
        $card = $this->newCard();
        $card->setAcquirerToken('acq');
        $this->assertTrue($card->validate());
        $this->assertEquals('acq', $card->getArray()[Fields::ACQUIRER_TOKEN]);
    }

    public function testSetBin()
    {
        $card = $this->newCard();
        $card->setbin('123');
        $this->assertTrue($card->validate());
        $this->assertEquals('123', $card->getArray()[Fields::CARD_BIN]);
    }

    public function testSetLast4()
    {
        $card = $this->newCard();
        $card->setLast4('1234');
        $this->assertTrue($card->validate());
        $this->assertEquals('1234', $card->getArray()[Fields::CARD_LAST4]);
    }
}
