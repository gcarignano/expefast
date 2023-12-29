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

/**
 * Encrypted Card Information for WHPP
 *
 * @see Gateway\Entities\WhppInfoInterface
 * @see Gateway\Entities\AbstractEntity
 * @final
 */
final class EncCardInfo extends AbstractEntity implements CardInfoType
{
    /**
     * Encrypted Card Holder Name
     *
     * @var string
     */
    protected $holderName;

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
     * Encryption algorithm
     *
     * @var string
     */
    protected $algo;

    /**
     * Key sequence number
     *
     * @var string
     */
    protected $seq;

    /**
     * @inheritdoc
     */
    protected $required = [
        "id",
        "holderName",
        "expiryYear",
        "expiryMonth",
        "acquirer",
        "algo",
        "seq"
    ];

    /**
     * @param string $number
     * @param string $name
     * @param string $year
     * @param string $month
     * @param string $acquirer
     * @param string $algo
     * @param string $seq
     */
    public function __construct($number, $name, $year, $month, $acquirer, $algo, $seq)
    {
        parent::__construct($number);
        $this->holderName = $name;
        $this->expiryYear = $year;
        $this->expiryMonth = $month;
        $this->algo = $algo;
        $this->seq = $seq;
        $this->acquirer = $acquirer;
    }

    /**
     * @inheritdoc
     */
    public function getArray()
    {
        $data = [
            Fields::CARD_ENC_NUMBER => $this->getId(),
            Fields::CARD_HOLDER => $this->holderName,
            Fields::CARD_YEAR => $this->expiryYear,
            Fields::CARD_MONTH => $this->expiryMonth,
            Fields::ACQUIRER => $this->acquirer,
            Fields::CARD_ENC_ALGO => $this->algo,
            Fields::CARD_ENC_KEYSEQ => $this->seq
        ];
        if ($this->cardType) {
            $data[Fields::CARDTYPE] = $this->cardType->getValue();
        }
        return $data;
    }

    /**
     * @inheritdoc
     */
    public function validate()
    {
        return (parent::validate()
            && Validator::isAlnumSpecial($this->getId(), 200)
            && Validator::isNum($this->expiryYear, 4)
            && Validator::isNum($this->expiryMonth, 2)
            && Validator::isUniAlnumSpecial($this->holderName, 150)
            && Validator::isAlnumSpecial($this->algo, 10)
            && Validator::isAlnumSpecial($this->seq, 100)
        );
    }

    /**
     * @return \Gateway\Entities\EncCardInfo
     */
    public function setCardType(CardTypes $cardType)
    {
        $this->cardType = $cardType;
        return $this;
    }
}
