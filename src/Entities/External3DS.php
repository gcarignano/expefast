<?php
/**
 * Copyright 2023 Connfido BV. All rights reserved.
 * Distribution of this software without explicit written consent from Newgen is
 * strictly prohibited. No part of this software must not be reverse engineered,
 * copied, reproduced or modified.
 */

namespace Gateway\Entities;

use Gateway\Common\Fields;
use Gateway\Common\Messages;
use Gateway\Common\ThreeDSStatus;
use Gateway\Exceptions\ValidationException;
use Gateway\Utility\Validator;

/**
 * External 3DS
 *
 * @see Gateway\Entities\AbstractEntity
 * @final
 */
final class External3DS extends AbstractEntity
{
    protected $data = [];

    /**
     * @inheritdoc
     */
    protected $required = [
        "data"
    ];

    /**
     * @inheritdoc
     */
    public function __construct(ThreeDSStatus $tdss)
    {
        $this->data[Fields::THREEDSSTATUS] = $tdss->getValue();
    }

    /**
     * Electronic Commerce Indicator (ECI) Values
     *
     * ECI Visa : ECI Mastercard : Status
     * ---------:----------------:-------
     * 5 : 02 : Authentication Successful
     * 6 : 01 : Attempts Processing Performed
     * 7 : 00 : Authentication Failed
     * 7 : 01 : Authentication Could Not Be Performed
     * 7 : 00 : Error
     *
     * @param string $value
     * @return \Gateway\Entities\External3DS
     */
    public function setECICode($value)
    {
        $this->data[Fields::ECICODE] = sprintf("%02d", $value);
        if (intval($value) >= 7) {
            $this->data[Fields::ECICODE] = strval($value);
        }
        return $this;
    }

    /**
     * Unique Identifier assigned by ACS to identify single transaction
     *
     * @param string $value
     * @return \Gateway\Entities\External3DS
     */
    public function setACSTransactionId($value)
    {
        $this->data[Fields::ACSTRANSACTIONID] = strval($value);
        return $this;
    }

    /**
     * Unique Identifier assigned by DS to identify single transaction
     *
     * @param string $value
     * @return \Gateway\Entities\External3DS
     */
    public function setDSTransactionId($value)
    {
        $this->data[Fields::DSTRANSACTIONID] = strval($value);
        return $this;
    }

    /**
     * Unique Identifier assigned by 3DS Server to identify single transaction
     *
     * @param string $value
     * @return \Gateway\Entities\External3DS
     */
    public function set3DSServerTransactionId($value)
    {
        $this->data[Fields::THREEDSSERVERTRANSACTIONID] = strval($value);
        return $this;
    }

    /**
     * 3DS version
     *
     * @param string $value
     * @return \Gateway\Entities\External3DS
     */
    public function set3DSVersion($value)
    {
        $this->data[Fields::THREEDSVERSION] = strval($value);
        return $this;
    }

    /**
     * An algorithm defined Payment system specific value either provided by
     * ACS or DS. It may be used for authentication
     *
     * @param string $value
     * @return \Gateway\Entities\External3DS
     */
    public function setAuthenticationValue($value)
    {
        $this->data[Fields::AUTHENTICATIONVALUE] = strval($value);
        return $this;
    }

    /**
     * @param string $value
     * @return \Gateway\Entities\External3DS
     */
    public function setXid($value)
    {
        $this->data[Fields::XID] = strval($value);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getArray()
    {
        return $this->data;
    }

    /**
     * @inheritdoc
     */
    public function validate()
    {
        foreach ($this->data as $key => $val) {
            if ($val === '') {
                throw new ValidationException("Empty value for $key");
            }
        }
        if (isset($this->data[Fields::ECICODE])
            && !in_array($this->data[Fields::ECICODE], ['00', '01', '02', '7', '6', '5'])
        ) {
            throw new ValidationException(Messages::ECI_VALUE);
        }
        return (
            parent::validate()
            && isset($this->data[Fields::THREEDSSTATUS])
        );
    }
}
