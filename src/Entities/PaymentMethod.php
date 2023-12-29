<?php
/**
 * Copyright 2023 Connfido BV. All rights reserved.
 * Distribution of this software without explicit written consent from Newgen is
 * strictly prohibited. No part of this software must not be reverse engineered,
 * copied, reproduced or modified.
 */

namespace Gateway\Entities;

use Gateway\Common\GatewayConstants;
use Gateway\Common\AbstractType;
use Gateway\Common\PaymentMode;
use Gateway\Common\Fields;
use Gateway\Common\Messages;
use Gateway\Utility\Validator;
use Gateway\Exceptions\ValidationException;
use Gateway\Exceptions\BadUsage;

/**
 * Payment Method
 *
 * @see Gateway\Entities\AbstractEntity
 * @final
 */
final class PaymentMethod extends AbstractEntity
{
    const HPP = 0;

    const WHPP = 1;

    const PLUGIN = 2;

    /**
     * Payment Mode
     *
     * @var \Gateway\Common\PaymentMode
     */
    protected $mode;

    /**
     * Payment type (HPP / WHPP / PLUGIN)
     *
     * @var string
     */
    protected $type = self::PLUGIN;

    /**
     * Additional information required for WHPP
     *
     * @var mixed
     */
    protected $whppData;

    /**
     * Additional Data
     *
     * @var \Gateway\Entities\PaymentMethod
     */
    protected $additionalData;

    /**
     * @var \Gateway\Entities\ThreeDS
     */
    protected $tds;

    /**
     * bankCode or cardType
     *
     * @var \Gateway\Common\AbstractType
     */
    protected $code;

    /**
     * @var boolean
     */
    protected $payout = false;

    /**
     * @var boolean
     */
    protected $allow3d;

    /**
     * @param \Gateway\Common\PaymentMode $paymentMode
     * @param \Gateway\Common\AbstractType $type
     */
    public function __construct(PaymentMode $paymentMode = null, AbstractType $type = null)
    {
        parent::__construct(null);
        $this->mode = $paymentMode;
        $this->code = $type;
        if (is_null($paymentMode)) {
            $this->asHPP();
        }
    }

    /**
     * Add Additional information required for WHPP
     *
     * @param \Gateway\Entities\WhppInfoInterface $additionalInfo
     * @return \Gateway\Entities\PaymentMethod
     */
    public function addWhppInfo(WhppInfoInterface $additionalInfo)
    {
        if ($this->mode->whppExtraInfo()) {
            $this->asWHPP()->whppData = $additionalInfo;
        }
        return $this;
    }

    public function addAdditionalData(WhppInfoInterface $additionalInfo)
    {
        if ($this->mode->allowsAdditionalData()) {
            $this->asWHPP()->additionalData = $additionalInfo;
        }
        return $this;
    }

    public function add3DS(ThreeDS $tds)
    {
        if ($tds) {
            $this->tds = $tds;
            $this->required[] = "tds";
        }
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getArray()
    {
        $data = $codeData = [];

        if ($this->tds) {
            if ($this->tds->hasExternal3DS()) {
                $this->disallow3D();
            }
            $data[Fields::THREE_DS] = $this->tds->getArray();
        }
        if (is_bool($this->allow3d)) {
            $data[Fields::ALLOW3D] = $this->allow3d;
        }

        if ($this->type == self::HPP) {
            return $data;
        }
        $data[Fields::PAYMENT_MODE] = $this->mode->getValue();
        if ($this->mode->canHaveType() && ($this->code instanceof AbstractType)) {
            $codeData[$this->mode->isCardMode() ? Fields::CARDTYPE : Fields::BANKCODE] = $this->code->getValue();
        }
        if ($this->type == self::WHPP) {
            $paymentData = $codeData;
            if ($this->whppData) {
                $paymentData = array_merge($paymentData, $this->whppData->getArray());
            }
            if ($this->additionalData) {
                $paymentToken = $this->additionalData->getArray();
                if ($this->mode->getValue() == GatewayConstants::APPLE_PAY) {
                    $paymentTokenValue = base64_decode($paymentToken[Fields::PAYMENT_TOKEN]);
                    $parsedToken = json_decode($paymentTokenValue, true);
                    if ($parsedToken['details']) {
                        $paymentToken[Fields::PAYMENT_TOKEN] = json_encode($parsedToken['details']);
                    } else if ($parsedToken['token']) {
                        $paymentToken[Fields::PAYMENT_TOKEN] = json_encode($parsedToken);
                    }
                }
                if ($this->mode->getValue() == GatewayConstants::GOOGLE_PAY) {
                    $paymentTokenValue = base64_decode($paymentToken[Fields::PAYMENT_TOKEN]);
                    $paymentToken[Fields::PAYMENT_TOKEN] = $paymentTokenValue;
                }
                $paymentData = array_merge($paymentData, $paymentToken);
            }
            $codeData = [
                Fields::PAYMENT_DETAIL => $paymentData
            ];
        }
        return array_merge($data, $codeData);
    }

    /**
     * Sets the payment type to HPP
     *
     * @return \Gateway\Entities\PaymentMethod
     */
    private function asHPP()
    {
        $this->type = self::HPP;
        return $this;
    }

    /**
     * Sets the payment type to WHPP
     *
     * @return \Gateway\Entities\PaymentMethod
     */
    private function asWHPP()
    {
        $this->type = self::WHPP;
        return $this;
    }

    /**
     * Check if payment is WHPP
     *
     * @return boolean
     */
    public function isWHPP()
    {
        return $this->type == self::WHPP;
    }

    /**
     * Enable payout
     *
     * @return \Gateway\Entities\PaymentMethod
     */
    public function asPayout()
    {
        $this->payout = true;
        return $this;
    }

    /**
     * Allow 3D payment
     *
     * @return \Gateway\Entities\PaymentMethod
     */
    public function allow3D()
    {
        $this->allow3d = true;
        return $this;
    }

    /**
     * Disallow 3D payment
     *
     * @return \Gateway\Entities\PaymentMethod
     */
    public function disallow3D()
    {
        $this->allow3d = false;
        return $this;
    }

    /**
     * Get Payment Methods related Auth Headers
     *
     * @return string
     */
    public function getReqAuthSignature()
    {
        switch ($this->type) {
            case self::HPP:
                return $this->payout
                    ? GatewayConstants::PAYMENT_TYPE_PAYOUT
                    : GatewayConstants::PAYMENT_TYPE_HPP;
            case self::WHPP:
                return $this->payout
                    ? GatewayConstants::PAYMENT_TYPE_WHPP_PAYOUT
                    : GatewayConstants::PAYMENT_TYPE_WITHOUT_HPP;
            case self::PLUGIN:
                return $this->payout
                    ? GatewayConstants::PAYMENT_TYPE_PAYOUT
                    : GatewayConstants::PAYMENT_TYPE_PLUGIN;
            default:
                throw new BadUsage(Messages::NO_AUTH_SIG);
        }
    }

    /**
     * @inheritdoc
     */
    public function validate()
    {
        if ($this->tds) {
            if ($this->tds->hasExternal3DS() && $this->type != self::WHPP) {
                throw new ValidationException(Messages::THREE_DS_EXT3DS);
            }
            if ($this->mode && !$this->mode->isCardMode() && $this->type != self::HPP) {
                throw new ValidationException(Messages::THREE_DS_NON_CARD);
            }
        }
        if ($this->type == self::HPP) {
            return true;
        }
        if ($this->payout && !$this->mode->isPayoutMode()) {
            throw new ValidationException(Messages::NO_PAYOUT);
        }
        if ($this->additionalData && !$this->additionalData->validate()) {
            throw new ValidationException("Additional payment info validation failed.");
        }
        if ($this->type == self::WHPP && $this->mode->whppExtraInfo() && !$this->whppData->validate()) {
                throw new ValidationException(Messages::WHPP_INFO);
        }
        return parent::validate();
    }
}
