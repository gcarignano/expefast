<?php
/**
 * Copyright 2023 Connfido BV. All rights reserved.
 * Distribution of this software without explicit written consent from Newgen is
 * strictly prohibited. No part of this software must not be reverse engineered,
 * copied, reproduced or modified.
 */

namespace Gateway\Common;

/**
 * 3DS Status Code
 *
 * @see Gateway\Common\ConstantObject
 * @final
 */
final class ThreeDSStatus extends ConstantObject
{
    /**
     * Authentication/Account Verification Successful
     *
     * @var \Gateway\Common\ThreeDSStatus
     */
    public static $Y;

    /**
     * Not Authenticated/Account Not Verified; Transaction denied
     *
     * @var \Gateway\Common\ThreeDSStatus
     */
    public static $N;

    /**
     * Authentication/Account Verification Could Not Be Performed; Technical or
     * other problem
     *
     * @var \Gateway\Common\ThreeDSStatus
     */
    public static $U;

    /**
     * Attempts Processing Performed; Not Authenticated/Verified, but a proof
     * of attempted authentication/verification is provided
     *
     * @var \Gateway\Common\ThreeDSStatus
     */
    public static $A;

    /**
     * Challenge Required; Additional authentication is required
     *
     * @var \Gateway\Common\ThreeDSStatus
     */
    public static $C;

    /**
     * Authentication/Account Verification Rejected; Issuer is rejecting
     * authentication/verification and request that authorisation not be
     * attempted.
     *
     * @var \Gateway\Common \ThreeDSStatus
     */
    public static $R;

    /**
     * @inheritdoc
     */
    public static function init()
    {
        self::$Y = new self("Y");
        self::$N = new self("N");
        self::$U = new self("U");
        self::$A = new self("A");
        self::$C = new self("C");
        self::$R = new self("R");
    }
}

ThreeDSStatus::init();
