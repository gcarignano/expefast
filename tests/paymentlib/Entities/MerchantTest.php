<?php

namespace GatewayTests\Entities;

use \Gateway\Common\Fields;
use \Gateway\Common\GatewayConstants;
use \Gateway\Exceptions\BadUsage;
use \Gateway\Entities\Merchant;

class MerchantTest extends \GatewayTests\TestCase
{
    protected $fake_crt;

    protected $fake_key;

    public function setUp() : void
    {
        $this->fake_crt = dirname(__FILE__) . '/fakecrt';
        $this->fake_key = dirname(__FILE__) . '/fakekey';
        foreach ([$this->fake_crt, $this->fake_key] as $f) {
            if (!file_exists($f)) {
                touch($f);
            }
        }
    }

    public function tearDown() : void
    {
        foreach ([$this->fake_crt, $this->fake_key] as $f) {
            if (file_exists($f)) {
                unlink($f);
            }
        }
    }

    public function testConstructor()
    {
        $merchant = new Merchant(
            'apikey',
            'token',
            $this->fake_crt,
            $this->fake_key
        );
        $this->assertTrue($merchant->validate());
        $this->assertEquals('apikey', $merchant->getArray()[Fields::MERCHANT_ID]);
        $this->assertEquals('token', $merchant->getApiToken());
        $this->assertEquals($this->fake_crt, $merchant->getCertFile());
        $this->assertEquals($this->fake_key, $merchant->getPrivateKeyFile());

        $this->assertEquals(GatewayConstants::TEST_API_ENDPOINT . '/partnerApi', $merchant->getPaymentEndpoint());
        $this->assertEquals(
            GatewayConstants::TEST_API_ENDPOINT . '/paymentLink/apikey',
            $merchant->getPaymentLinkEndpoint()
        );
        $this->assertEquals(
            GatewayConstants::TEST_API_ENDPOINT . '/apikey',
            $merchant->getApiKeyEndpoint()
        );
    }

    public function testCustomEndpoints()
    {
        $customEndpoint = 'https://arbitrary.apiurl.com';
        $merchant = new Merchant(
            'apikey',
            'token',
            $this->fake_crt,
            $this->fake_key,
            $customEndpoint
        );
        $this->assertEquals($customEndpoint . '/partnerApi', $merchant->getPaymentEndpoint());
        $this->assertEquals(
            $customEndpoint . '/paymentLink/apikey',
            $merchant->getPaymentLinkEndpoint()
        );
        $this->assertEquals(
            $customEndpoint . '/apikey',
            $merchant->getApiKeyEndpoint()
        );

        $merchant = new Merchant(
            'apikey',
            'token',
            $this->fake_crt,
            $this->fake_key,
            $customEndpoint . '/partnerApi'
        );
        $this->shouldThrowException(function () use ($merchant) {
            $merchant->validate();
        }, null, BadUsage::class);
    }

    public function testLive()
    {
        $merchant = new Merchant(
            'apikey',
            'token',
            $this->fake_crt,
            $this->fake_key
        );
        $merchant->live();
        $this->assertTrue($merchant->validate());
        $this->assertEquals(GatewayConstants::API_ENDPOINT . '/partnerApi', $merchant->getPaymentEndpoint());
        $this->assertEquals(
            GatewayConstants::API_ENDPOINT . '/paymentLink/apikey',
            $merchant->getPaymentLinkEndpoint()
        );
        $this->assertEquals(
            GatewayConstants::API_ENDPOINT . '/apikey',
            $merchant->getApiKeyEndpoint()
        );
    }
}
