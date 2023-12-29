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
use Gateway\Exceptions\ValidationException;
use Gateway\Utility\Validator;

/**
 * 3DS for Card payments
 *
 * @see Gateway\Entities\AbstractEntity
 * @final
 */
final class ThreeDS extends AbstractEntity
{
    /**
     * @var int
     */
    protected $challengeWindowSize;

    /**
     * @var int
     */
    protected $challengeIndicator;

    /**
     * @var boolean
     */
    protected $lowValue;

    /**
     * @var boolean
     */
    protected $tra;

    /**
     * @var boolean
     */
    protected $trustedBeneficiary;

    /**
     * @var boolean
     */
    protected $secureCorporatePayment;

    /**
     * @var boolean
     */
    protected $recurringMITExemptionOther;

    /**
     * @var boolean
     */
    protected $recurringMITExemptionSameAmount;

    /**
     * @var boolean
     */
    protected $delegatedAuthentication;

    /**
     * @var string
     */
    protected $vmid;

    /**
     * @var \Gateway\Entities\External3DS
     */
    protected $external3DS;

    protected $allowedFields = [
        Fields::TIMEZONE,
        Fields::BROWSERCOLORDEPTH,
        Fields::BROWSERLANGUAGE,
        Fields::BROWSERSCREENHEIGHT,
        Fields::BROWSERSCREENWIDTH,
        Fields::OS,
        Fields::BROWSERACCEPTHEADER,
        Fields::USERAGENT,
        Fields::BROWSERJAVASCRIPTENABLED,
        Fields::BROWSERJAVAENABLED,
        Fields::ACCEPTCONTENT,
        Fields::BROWSERIP,
    ];

    protected $allowedSDKFields = [
        Fields::SDKAPPID,
        Fields::SDKENCDATA,
        Fields::SDKEPHEMPUBKEY,
        Fields::SDKMAXTIMEOUT,
        Fields::SDKREFERENCENUMBER,
        Fields::SDKTRANSID,
    ];

    protected $fingerprintData = [];

    protected $sdkData = [];

    /**
     * @inheritdoc
     */
    public function __construct(array $arr = null)
    {
        $this->addFingerprintFields($arr);
    }

    /**
     * Add 3DS2 device fingerprint fields.
     * Array format should be [<3DS Field> => <3DS Value>]
     *
     * @param array $arr
     * @return \Gateway\Entities\ThreeDS
     */
    public function addFingerprintFields(array $arr = null)
    {
        if (!empty($arr)) {
            foreach ($arr as $field => $value) {
                if (in_array($field, $this->allowedFields)) {
                    $this->fingerprintData[$field] = $value;
                }
            }
        }
         return $this;
    }

    /**
     * Add 3DS SDK fields.
     * Array format should be [<SDK Field> => <Value>]
     *
     * @param array $arr
     * @return \Gateway\Entities\ThreeDS
     */
    public function addSDKFields(array $arr = null)
    {
        if (!empty($arr)) {
            foreach ($arr as $field => $value) {
                if (in_array($field, $this->allowedSDKFields)) {
                    $this->sdkData[$field] = $value;
                }
            }
        }
         return $this;
    }

    /**
     * Add External 3DS
     * Only supported in WHPP
     *
     * @param External3DS $ext3ds
     * @return \Gateway\Entities\ThreeDS
     */
    public function addExternal3DS(External3DS $ext3ds)
    {
        $this->external3DS = $ext3ds;
        $this->required[] = "external3DS";
        return $this;
    }

    /**
     * Check whether External 3DS is present
     *
     * @return boolean
     */
    public function hasExternal3DS()
    {
        return $this->external3DS != null;
    }

    /**
     * Challenge preference for 3DS2
     *
     * Value : Preference
     * ------:-----------
     * 01 : No Preference (default)
     * 02 : No Challenge Requested
     * 03 : Challenge requested
     * 04 : Challenge requested -> Mandate
     *
     * @param int $value
     * @return \Gateway\Entities\ThreeDS
     */
    public function setChallengeIndicator($value = 1)
    {
        $this->challengeIndicator = intval($value);
        return $this;
    }

    /**
     * Preferred Challenge window size
     *
     * Value : window size
     * ------:------------
     * 01 : 250 X 400
     * 02 : 390 X 400
     * 03 : 500 X 600
     * 04 : 600 X 400
     * 05 : Full Screen (default)
     *
     * @param int $value
     * @return \Gateway\Entities\ThreeDS
     */
    public function setChallengeWindowSize($value = 5)
    {
        $this->challengeWindowSize = intval($value);
        return $this;
    }

    /**
     * Low value exemption
     *
     * @return \Gateway\Entities\ThreeDS
     */
    public function exemptLowValue()
    {
        $this->lowValue = true;
        return $this;
    }

    /**
     * TRA exemption
     *
     * @return \Gateway\Entities\ThreeDS
     */
    public function exemptTRA()
    {
        $this->tra = true;
        return $this;
    }

    /**
     * Trusted beneficiary exemption
     *
     * @return \Gateway\Entities\ThreeDS
     */
    public function exemptTrustedBeneficiary()
    {
        $this->trustedBeneficiary = true;
        return $this;
    }

    /**
     * Corporate card payment exemption
     *
     * @return \Gateway\Entities\ThreeDS
     */
    public function exemptSecureCorporatePayment()
    {
        $this->secureCorporatePayment = true;
        return $this;
    }

    /**
     * @return \Gateway\Entities\ThreeDS
     */
    public function exemptRecurringMITOther()
    {
        $this->recurringMITExemptionOther = true;
        return $this;
    }

    /**
     * @return \Gateway\Entities\ThreeDS
     */
    public function exemptRecurringMITSameAmount()
    {
        $this->recurringMITExemptionSameAmount = true;
        return $this;
    }

    /**
     * @return \Gateway\Entities\ThreeDS
     */
    public function exemptDelegatedAuthentication()
    {
        $this->delegatedAuthentication = true;
        return $this;
    }

    /**
     * Visa Merchant Identifier – Required in case the TrustedBeneficiary
     * Exemption is being applied with Visa Secure
     *
     * @param string $value
     * @return \Gateway\Entities\ThreeDS
     */
    public function exemptVMID($value)
    {
        $this->vmid = $value;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getArray()
    {
        $data = [
            Fields::EXEMPTIONS => [
                Fields::LOWVALUE => filter_var($this->lowValue, FILTER_VALIDATE_BOOLEAN),
                Fields::TRA => filter_var($this->tra, FILTER_VALIDATE_BOOLEAN),
                Fields::TRUSTEDBENEFICIARY => filter_var($this->trustedBeneficiary, FILTER_VALIDATE_BOOLEAN),
                Fields::SECURECORPORATEPAYMENT => filter_var($this->secureCorporatePayment, FILTER_VALIDATE_BOOLEAN),
                Fields::RECURRING_EXEMPTION_OTHER => filter_var(
                    $this->recurringMITExemptionOther,
                    FILTER_VALIDATE_BOOLEAN
                ),
                Fields::RECURRING_EXEMPTION_SAMEAMOUNT => filter_var(
                    $this->recurringMITExemptionSameAmount,
                    FILTER_VALIDATE_BOOLEAN
                ),
                Fields::DELEGATEDAUTHENTICATION => filter_var($this->delegatedAuthentication, FILTER_VALIDATE_BOOLEAN),

            ]
        ];
        if (!empty($this->fingerprintData)) {
            $data[Fields::FINGERPRINT] = $this->fingerprintData;
        }
        if (!empty($this->sdkData)) {
            $data[Fields::SDK] = $this->sdkData;
        }
        if (is_int($this->challengeWindowSize)) {
            $data[Fields::CHALLENGEWINDOWSIZE] = sprintf("%02d", $this->challengeWindowSize);
        }
        if (is_int($this->challengeIndicator)) {
            $data[Fields::CHALLENGEINDICATOR] = sprintf("%02d", $this->challengeIndicator);
        }
        if ($this->vmid != null) {
            $data[Fields::EXEMPTIONS][Fields::VMID] = $this->vmid;
        }
        if ($this->external3DS) {
            $data[Fields::EXTERNALTHREEDS] = $this->external3DS->getArray();
        }
        return $data;
    }

    /**
     * @inheritdoc
     */
    public function validate()
    {
        if (is_int($this->challengeWindowSize)
            && ($this->challengeWindowSize < 1 || $this->challengeWindowSize > 5)
        ) {
            throw new ValidationException(Messages::CHALLENGE_WINSIZE, $this->challengeWindowSize);
        }
        if (is_int($this->challengeIndicator)
            && ($this->challengeIndicator < 1 || $this->challengeIndicator > 4)
        ) {
            throw new ValidationException(Messages::CHALLENGE_INDICATOR, $this->challengeIndicator);
        }
        return parent::validate();
    }
}
