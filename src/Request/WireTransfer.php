<?php
/**
 * Copyright 2023 Connfido BV. All rights reserved.
 * Distribution of this software without explicit written consent from Newgen is
 * strictly prohibited. No part of this software must not be reverse engineered,
 * copied, reproduced or modified.
 */

namespace Gateway\Request;

use Gateway\Entities\AbstractWireTransferBank;
use Gateway\Common\Currency;
use Gateway\Common\Fields;
use Gateway\Common\GatewayConstants;
use Gateway\Entities\WireTransferBank;
use Gateway\Entities\WireTransferBankEEA;
use Gateway\HttpClient\GatewayResponse;
use Gateway\Utility\Validator;

/**
 * WireTransfer
 *
 * @see Gateway\Request\AbstractRequest
 */
class WireTransfer extends AbstractRequest
{
    /**
     * Query Wiretransfer bank
     */
    const QUERY = 1;

    /**
     * Create Wiretransfer bank
     */
    const CREATE = 3;

    /**
     *  Remove Wiretansfer bank
     */
    const REMOVE = 2;

    /**
     * Update Wiretransfer bank
     */
    const UPDATE = 4;

    /**
     * @var \Gateway\Entities\AbstractWireTransferBank
     */
    protected $bank;

    /**
     * @var bool
     */
    protected $default = false;

    /**
     * Transaction Currency Code
     *
     * @var \Gateway\Common\Currency
     */
    protected $currencyCode;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var string
     */
    protected $accountHolderName;

    /**
     * @var string
     */
    protected $accountHolderAddress;

    /**
     * @param \Gateway\Common\Currency $currencyCode
     * @param string $accountHolderName
     * @param string $accountHolderAddress
     * @param \Gateway\Entities\WireTransferBank $bank
     * @param boolean $default
     * @param string $description
     */
    public function __construct(
        Currency $currencyCode = null,
        $accountHolderName = "",
        $accountHolderAddress = "",
        AbstractWireTransferBank $bank = null,
        $default = false,
        $description = ""
    ) {
        parent::__construct(null);
        $this->default = filter_var($default, FILTER_VALIDATE_BOOLEAN);
        $this->description = $description;
        $this->accountHolderName = $accountHolderName;
        $this->accountHolderAddress = $accountHolderAddress;
        $this->currencyCode = $currencyCode;
        $this->bank = $bank;
    }

    /**
     * set Default
     *
     * @param bool $default
     */
    public function setDefault($default)
    {
        $this->default = $default;
        return $this;
    }

    /**
     * set Description
     *
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * set Account Holder Name
     *
     * @param string $accountHolderName
     */
    public function setAccountHolderName($accountHolderName)
    {
        $this->accountHolderName = $accountHolderName;
        return $this;
    }

    /**
     * set Account Holder Address
     *
     * @param string $accountHolderAddress
     */
    public function setAccountHolderAddress($accountHolderAddress)
    {
        $this->setAccountHolderAddress = $accountHolderAddress;
        return $this;
    }

    /**
     * set Currency Code
     *
     * @param \Gateway\Common\Currency $currencyCode
     */
    public function setCurrencyCode(Currency $currencyCode)
    {
        $this->currencyCode = $currencyCode;
        return $this;
    }

    /**
     * set Bank
     *
     * @param \Gateway\Entities\WireTransferBank $bank
     */
    public function setBank(AbstractWireTransferBank $bank){
        $this->bank = $bank;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getArray()
    {
        $data = array_merge(
            $this->merchant->getArray(),
            [
                Fields::WIRETRANSFER_DEFAULT => $this->default,
            ]
        );
        if ($this->currencyCode) {
            $data[Fields::CURRENCYCODE] = $this->currencyCode->getValue();
        }
        if (!empty($this->accountHolderName) || !empty($this->accountHolderAddress)) {
            $data[Fields::WIRETRANSFER_ACCOUNT_HOLDER] = [];
            if (!empty($this->accountHolderAddress)) {
                $data[Fields::WIRETRANSFER_ACCOUNT_HOLDER][Fields::WIRETRANSFER_ACCOUNT_HOLDER_ADDRESS] = $this->accountHolderAddress;
            }
            if (!empty($this->accountHolderName)) {
                $data[Fields::WIRETRANSFER_ACCOUNT_HOLDER][Fields::WIRETRANSFER_BANK_ACCOUNT_HOLDER_NAME] = $this->accountHolderName;
            }
        }
        if ($this->bank) {
            $data[Fields::WIRETANSFER_ISEEA] = $this->bank->getEEA();
            $data[Fields::WIRETRANSFER_BANK] = $this->bank->getArray();
        }
        if (!empty($this->description)) {
            $data[Fields::WIRETRANSFER_DESCRIPTION] = $this->description;
        }
        return $data;
    }

    /**
     * @inheritdoc
     */
    public function validate()
    {
        if ($this->bank && !Validator::isValidEntity($this->bank)) {
            return false;
        }
        return parent::validate()
            && Validator::isUniAlnumSpecial($this->description, 255, true)
            && Validator::isAlnumSpecial($this->accountHolderName, 150, true)
            && Validator::isAlnumSpecial($this->accountHolderAddress, 250, true);
    }

    /**
     * @inheritdoc
     */
    public function getMethod()
    {
        $method = [
            self::QUERY => "GET",
            self::CREATE => "POST",
            self::REMOVE => "DELETE",
            self::UPDATE => "PUT"
        ];
        return $method[$this->operation];
    }

    /**
     * Create WireTransfer Bank
     *
     * @return \Gateway\Response
     */
    public function create()
    {
        $this->required = [
            'merchant',
            'currencyCode',
            'bank',
            'accountHolderName',
            'accountHolderAddress'
        ];
        $this->operation = self::CREATE;
        $this->authSignature = GatewayConstants::API_TYPE_WIRETRANS_SAVE;
        return $this->send();
    }

    /**
     * Update WireTransfer Bank details
     *
     * @param string $id
     * @return \Gateway\Response
     */
    public function update($id)
    {
        $this->setId($id);
        $this->required = ['merchant'];
        $this->operation = self::UPDATE;
        $this->authSignature = GatewayConstants::API_TYPE_WIRETRANS_UPDATE;
        return $this->send();
    }

    /**
     * Get WireTransfer Bank details
     *
     * @param string $id
     * @return \Gateway\Response
     */
    public function details($id)
    {
        $this->setId($id);
        $this->operation = self::QUERY;
        $this->auth = false;
        return $this->send();
    }

    /**
     * Remove WireTransfer Bank details
     *
     * @param string $id
     * @return \Gateway\Response
     */
    public function remove($id)
    {
        $this->setId($id);
        $this->operation = self::REMOVE;
        $this->auth = false;
        return $this->send();
    }

    /**
     * @inheritdoc
     */
    public function getEndpoint()
    {
        $suffix = '';
        if ($this->operation != self::CREATE) {
            $suffix = '/' . $this->getId();
        }
        return $this->merchant->getApiKeyEndpoint() . '/wire-transfer' . $suffix;
    }

    /**
     * @inheritdoc
     */
    public function hasPayload()
    {
        return in_array($this->operation, [self::CREATE, self::UPDATE]);
    }

    /**
     * @inheritdoc
     */
    public function process(GatewayResponse $response)
    {
        $res = parent::process($response);
        if (($data = $res->getData()) && @$data['statusCode'] != 200) {
            $res->badResponse();
        }
        return $res;
    }
}
