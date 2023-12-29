<?php

namespace GatewayTests\Entities;

use \Gateway\Common\Fields;
use \Gateway\Common\NotificationChannel;
use \Gateway\Entities\NotificationSetting;

class NotificationSettingTest extends \GatewayTests\TestCase
{
    public function testConstructor()
    {
        $ns = new NotificationSetting(
            NotificationChannel::$EMAIL,
            "Message Body"
        );
        $this->assertTrue($ns->validate());
        $this->assertIsArray($ns->getArray());
        $this->assertEquals(1, count($ns->getArray()));

        foreach ([
            Fields::CHANNEL_NAME => NotificationChannel::$EMAIL->getValue(),
            Fields::CHANNEL_VALUE => "Message Body"
        ] as $k => $v) {
            $this->assertEquals($v, $ns->getArray()[0][$k]);
        }

        $this->shouldThrowException(function () {
            $ns = new NotificationSetting(
                NotificationChannel::$EMAIL,
                "aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa"
                . "aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa"
                . "aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa"
                . "aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa"
                . "aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa"
                . "aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa"
            );
            $ns->validate();
        });
    }

    public function testAddChannel()
    {
        $ns = new NotificationSetting(
            NotificationChannel::$EMAIL,
            "Message Body"
        );
        $ns->addChannel(NotificationChannel::$SMS, "Body Message");
        $this->assertTrue($ns->validate());
        $this->assertIsArray($ns->getArray());
        $this->assertEquals(2, count($ns->getArray()));

        foreach ([
            Fields::CHANNEL_NAME => NotificationChannel::$SMS->getValue(),
            Fields::CHANNEL_VALUE => "Body Message"
        ] as $k => $v) {
            $this->assertEquals($v, $ns->getArray()[1][$k]);
        }
    }
}
