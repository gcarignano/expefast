<?php
/**
 * Copyright 2023 Connfido BV. All rights reserved.
 * Distribution of this software without explicit written consent from Newgen is
 * strictly prohibited. No part of this software must not be reverse engineered,
 * copied, reproduced or modified.
 */

namespace Gateway\Entities;

use Gateway\Utility\Validator;
use Gateway\Common\CardTypes;
use Gateway\Common\Fields;
use Gateway\Common\Messages;
use Gateway\Exceptions\ValidationException;

/**
 * Card Information for WHPP
 *
 * @see Gateway\Entities\WhppInfoInterface
 * @see Gateway\Entities\AbstractEntity
 * @final
 */
final class CardInfo extends AbstractEntity implements CardInfoType
{
    protected $isToken = false;

    /**
     * Card Holder Name
     *
     * @var string
     */
    protected $holderName;

    /**
     * CVV Number
     *
     * @var string
     */
    protected $cvv;

    /**
     * Card Expiry Year
     *
     * @var string
     */
    protected $expiryYear;

    /**
     * Card Expiry Month
     *
     * @var string
     */
    protected $expiryMonth;

    /**
     * Card Type
     *
     * @var \Gateway\Common\CardTypes
     */
    protected $cardType;

    /**
     * Acquirer
     *
     * @var string
     */
    protected $acquirer;

    /**
     * Acquirer Token
     *
     * @var string
     */
    protected $acquirerToken;

    /**
     * Bin
     *
     * @var string
     */
    protected $bin;

    /**
     * Last4
     *
     * @var string
     */
    protected $last4;

    /**
     * @var boolean
     */
    protected $saveCard;

    /**
     * @inheritdoc
     */
    protected $required = [
        "id",
        "holderName",
        "cvv",
        "expiryYear",
        "expiryMonth"
    ];

    /**
     * @param string $number
     * @param string $name
     * @param string $cardCVV
     * @param string $year
     * @param string $month
     * @param boolean $save
     */
    public function __construct($number, $name = null, $cardCVV = null, $year = null, $month = null, $save = false)
    {
        parent::__construct($number);
        $this->holderName = $name;
        $this->cvv = $cardCVV;
        $this->expiryYear = $year;
        $this->expiryMonth = $month;
        $this->saveCard = filter_var($save, FILTER_VALIDATE_BOOLEAN);
        if (empty($name)) {
            $this->asToken();
        }
    }

    /**
     * @inheritdoc
     */
    public function getArray()
    {
        $data = [
            Fields::CARD_TOKEN => $this->getId()
        ];
        if (!$this->isToken) {
            $data = [];

            if ($this->getId()) {
                $data[Fields::CARD_NUMBER] = $this->getId();
            }
            if ($this->holderName) {
                $data[Fields::CARD_HOLDER] = $this->holderName;
            }
            if ($this->expiryYear) {
                $data[Fields::CARD_YEAR] = $this->expiryYear;
            }
            if ($this->expiryMonth) {
                $data[Fields::CARD_MONTH] = $this->expiryMonth;
            }
            if ($this->saveCard) {
                $data[Fields::CARD_SAVE] = $this->saveCard;
            }
            if ($this->cardType) {
                $data[Fields::CARDTYPE] = $this->cardType->getValue();
            }
            if ($this->acquirer) {
                $data[Fields::ACQUIRER] = $this->acquirer;
            }
            if ($this->acquirerToken) {
                $data[Fields::ACQUIRER_TOKEN] = $this->acquirerToken;
            }
            if ($this->bin) {
                $data[Fields::CARD_BIN] = $this->bin;
            }
            if ($this->last4) {
                $data[Fields::CARD_LAST4] = $this->last4;
            }
        }
        if ($this->cvv) {
            $data[Fields::CARD_CVV] = $this->cvv;
        }
        if ($this->saveCard) {
            $data[Fields::CARD_SAVE] = $this->saveCard;
        }
        return $data;
    }

    /**
     * @inheritdoc
     */
    public function validate()
    {
        return (parent::validate()
            && Validator::isAlnumSpecial($this->getId(), $this->isToken ? 100 : 32)
            && Validator::isNum($this->expiryYear, 4, $this->isToken)
            && Validator::isNum($this->expiryMonth, 2, $this->isToken)
            && Validator::isNum($this->cvv, 4, $this->isToken)
            && Validator::isUniAlnumSpecial($this->holderName, 150, $this->isToken)
        );
    }

    /**
     * Use token with card info
     *
     * @return \Gateway\Entities\CardInfo
     */
    public function asToken()
    {
        $this->isToken = true;
        $this->required = ["id"];
        return $this;
    }

    /**
     * @return \Gateway\Entities\CardInfo
     */
    public function setCardType(CardTypes $cardType)
    {
        $this->cardType = $cardType;
        return $this;
    }

    /**
     * @return \Gateway\Entities\CardInfo
     */
    public function setAcquirer($acquirer)
    {
        $this->acquirer = $acquirer;
        return $this;
    }

    /**
     * @return \Gateway\Entities\CardInfo
     */
    public function setAcquirerToken($acquirerToken)
    {
        $this->acquirerToken = $acquirerToken;
        return $this;
    }

    /**
     * @return \Gateway\Entities\CardInfo
     */
    public function setBin($bin)
    {
        $this->bin = $bin;
        return $this;
    }

    /**
     * @return \Gateway\Entities\CardInfo
     */
    public function setLast4($last4)
    {
        $this->last4 = $last4;
        return $this;
    }

    /**
     * @return \Gateway\Entities\CardInfo
     */
    public function saveCardDetails($save = true)
    {
        $this->saveCard = filter_var($save, FILTER_VALIDATE_BOOLEAN);
        return $this;
    }
}
