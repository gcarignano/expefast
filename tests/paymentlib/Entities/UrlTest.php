<?php

namespace GatewayTests\Entities;

use \Gateway\Common\Fields;
use \Gateway\Entities\Url;

class UrlTest extends \GatewayTests\TestCase
{
    protected function newUrl()
    {
        $success_url = 'https://somesite.me/order/success';
        $cancel_url = 'https://somesite.me/order/cancel';
        $fail_url = 'https://somesite.me/order/fail';
        $cart_url = 'https://somesite.me/cart';

        return new Url($success_url, $fail_url, $cancel_url, $cart_url);
    }

    public function testConstructor()
    {
        $success_url = 'https://somesite.me/order/success';
        $cancel_url = 'https://somesite.me/order/cancel';
        $fail_url = 'https://somesite.me/order/fail';
        $cart_url = 'https://somesite.me/cart';

        $url = new Url($success_url);
        $this->assertTrue($url->validate());
        foreach ([Fields::URL_FAIL, Fields::URL_CANCEL, Fields::URL_SUCCESS] as $v) {
            $this->assertEquals($success_url, $url->getArray()[$v]);
        }

        $url = new Url($success_url, $fail_url);
        $this->assertTrue($url->validate());
        foreach ([
            Fields::URL_FAIL => $fail_url,
            Fields::URL_CANCEL => $fail_url,
            Fields::URL_SUCCESS => $success_url,
        ] as $k => $v) {
            $this->assertEquals($v, $url->getArray()[$k]);
        }

        $url = new Url($success_url, null, $cancel_url);
        $this->assertTrue($url->validate());
        foreach ([
            Fields::URL_FAIL => $cancel_url,
            Fields::URL_CANCEL => $cancel_url,
            Fields::URL_SUCCESS => $success_url,
        ] as $k => $v) {
            $this->assertEquals($v, $url->getArray()[$k]);
        }

        $url = new Url($success_url, $fail_url, $cancel_url, $cart_url, true);
        $this->assertTrue($url->validate());
        foreach ([
            Fields::URL_FAIL => $fail_url,
            Fields::URL_CANCEL => $cancel_url,
            Fields::URL_SUCCESS => $success_url,
            Fields::URL_CART => $cart_url,
            Fields::CONFIRMATIONPAGE => 'true',
        ] as $k => $v) {
            $this->assertEquals($v, $url->getArray()[$k]);
        }

        $this->shouldThrowException(function () {
            (new Url('http:this_is_not_a_valid_url.com'))->validate();
        });
    }

    public function testIFrame()
    {
        $url = $this->newUrl();
        $this->assertFalse($url->getArray()[Fields::IFRAME]);

        $url->iFrame();
        $this->assertTrue($url->validate());
        $this->assertTrue($url->getArray()[Fields::IFRAME]);
    }

    public function testSetPrivacyUrl()
    {
        $url = $this->newUrl()->setPrivacyUrl("https://some.url.com");
        $this->assertTrue($url->validate());
        $this->assertEquals("https://some.url.com", $url->getArray()[Fields::URL_PRIVACY]);
    }

    public function testSetTermsUrl()
    {
        $url = $this->newUrl()->setTermsUrl("https://some.url.com");
        $this->assertTrue($url->validate());
        $this->assertEquals("https://some.url.com", $url->getArray()[Fields::URL_TERMS]);
    }

    public function testSetProductUrl()
    {
        $url = $this->newUrl()->setProductURL("https://some.url.com");
        $this->assertTrue($url->validate());
        $this->assertEquals("https://some.url.com", $url->getArray()[Fields::URL_PRODUCT]);
    }
}
