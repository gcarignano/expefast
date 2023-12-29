<?php
/**
 * Copyright 2023 Connfido BV. All rights reserved.
 * Distribution of this software without explicit written consent from Newgen is
 * strictly prohibited. No part of this software must not be reverse engineered,
 * copied, reproduced or modified.
 */

namespace Gateway\Entities;

use Gateway\Utility\Validator;
use Gateway\Utility\Util;
use Gateway\Common\Fields;

/**
 * Url
 *
 * @see Gateway\Entities\AbstractEntity
 * @final
 */
final class Url extends AbstractEntity
{
    /**
     * Url for Successfull transaction
     *
     * @var string
     */
    protected $success;

    /**
     * Url for Cancelled transaction
     *
     * @var string
     */
    protected $cancel;

    /**
     * Url for Failed transaction
     *
     * @var string
     */
    protected $fail;

    /**
     * Url for webshop shopping cart
     *
     * @var string
     */
    protected $cart;

    /**
     * Url for Privacy policy
     *
     * @var string
     */
    protected $privacy;

    /**
     * Url for Terms and conditions
     *
     * @var string
     */
    protected $terms;

    /**
     * Url for product
     *
     * @var string
     */
    protected $product;

    /**
     * Show confirmation page on Gateway
     *
     * @var boolean
     */
    protected $confirmationPage;

    /**
     * @var boolean
     */
    protected $asiFrame = false;

    /**
     * @inheritdoc
     */
    protected $required = [
        "success", "fail", "cancel"
    ];

    /**
     * @param string $success
     * @param string $fail
     * @param string $cancel
     * @param string $cart
     * @param boolean $confirmationPage
     */
    public function __construct(
        $success,
        $fail = null,
        $cancel = null,
        $cart = null,
        $confirmationPage = false
    ) {
        if (is_null($fail) && is_null($cancel)) {
            $fail = $cancel = $success;
        }
        if (is_null($fail) || is_null($cancel)) {
            $fail = $cancel = implode('', [ $fail, $cancel ]);
        }
        $this->success = Util::prependSchema($success);
        $this->fail = Util::prependSchema($fail);
        $this->cancel = Util::prependSchema($cancel);
        $this->cart = Util::prependSchema($cart);
        $this->confirmationPage = filter_var($confirmationPage, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * @inheritdoc
     */
    public function getArray()
    {
        $data = [
            Fields::URL_SUCCESS => $this->success,
            Fields::URL_CANCEL => $this->cancel,
            Fields::URL_FAIL => $this->fail,
            Fields::CONFIRMATIONPAGE => var_export($this->confirmationPage, true),
            Fields::IFRAME => $this->asiFrame
        ];
        if (! empty($this->cart)) {
            $data[Fields::URL_CART] = $this->cart;
        }
        if (! empty($this->privacy)) {
            $data[Fields::URL_PRIVACY] = $this->privacy;
        }
        if (! empty($this->terms)) {
            $data[Fields::URL_TERMS] = $this->terms;
        }
        if (! empty($this->product)) {
            $data[Fields::URL_PRODUCT] = $this->product;
        }
        return $data;
    }

    /**
     * iFrame
     *
     * @return \Gateway\Entities\Url
     */
    public function iFrame()
    {
        $this->asiFrame = true;
        return $this;
    }

    /**
     * Set Privacy policy URL
     *
     * @param string $url
     * @return \Gateway\Entities\Url
     */
    public function setPrivacyUrl($url)
    {
        $this->privacy = Util::prependSchema($url);
        return $this;
    }

    /**
     * Set Terms and conditions URL
     *
     * @param string $url
     * @return \Gateway\Entities\Url
     */
    public function setTermsUrl($url)
    {
        $this->terms = Util::prependSchema($url);
        return $this;
    }

    /**
     * Set Product URL
     *
     * @param string $url
     * @return \Gateway\Entities\Url
     */
    public function setProductUrl($url)
    {
        $this->product = Util::prependSchema($url);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function validate()
    {
        return (
            Validator::isUrl($this->success)
            && Validator::isUrl($this->cancel)
            && Validator::isUrl($this->fail)
            && Validator::isUrl($this->cart, true)
            && Validator::isUrl($this->privacy, true)
            && Validator::isUrl($this->terms, true)
            && Validator::isUrl($this->product, true)
        );
    }
}
