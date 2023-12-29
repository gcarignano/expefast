<?php
/**
 * Copyright 2023 Connfido BV. All rights reserved.
 * Distribution of this software without explicit written consent from Newgen is
 * strictly prohibited. No part of this software must not be reverse engineered,
 * copied, reproduced or modified.
 */

namespace Gateway\Common;

/**
 * Error or warning Messages
 *
 * @abstract
 */
abstract class Messages
{
    const CURL = 'Invalid cURL Resource';

    const BIC = 'Invalid BIC';

    const IBAN = 'Invalid IBAN';

    const AC_HOLDER = 'Invalid Bank account holder name';

    const ITEM_DETAIL = 'Invalid Item details';

    const NULL_CUSTOMERID = 'CustomerID is not set';

    const NO_AUTH_SIG = 'No AuthSignature for this type';

    const PAYMODE = 'Invalid PaymentMode';

    const NO_PAYOUT = 'Payout is not available for this PaymentMode';

    const WHPP_INFO = 'Invalid or missing info for initiating WHPP';

    const SUMMARY_DETAIL = 'Invalid Summary details';

    const SUMMARY_DISCOUNT = 'Invalid Summary discount';

    const CLIENT_UNINITIALIZED = 'HTTP client not initialized';

    const REQUEST_INVALIDATED = 'Request field validation failed';

    const AUTH_DISABLED = 'Auth disabled for this request';

    const LOCALE = 'Invalid Locale';

    const SEND_DISABLED = 'send() is disabled for this request.';

    const URL = 'Invalid URL';

    const AMOUNT = 'Invalid Amount';

    const ZERO_AUTH_AMOUNT = 'Invalid Amount for Zero Auth';

    const ITEM_AMOUNT = 'Invalid Item Amount';

    const EMAIL = 'Invalid email';

    const NUMBER = 'Invalid number';

    const INVALID = 'Invalid value';

    const INVALID_IP = 'Invalid IP Address';

    const INVALID_DOB = 'Invalid DOB';

    const ENTITY = 'Invalid entity';

    const UNREADABLE = 'File do not exists or is unreadable';

    const CURRENCY = 'Invalid Currency Code';

    const COMPANY = 'Invalid Company Details';

    const ORIGIN = 'Invalid origin/referrer';

    const WEBHOOK_DATA = 'Invalid webhook data';

    const ESYNC = 'Synchronous payment works only with WHPP';

    const BAD_TOKEN = 'Token invalid';

    const THREE_DS_NON_CARD = '3DSecure not allowed on non-card payment mode';

    const THREE_DS_EXT3DS = 'external3DS is only supported in withoutHpp';

    const CHALLENGE_INDICATOR = 'Challenge indicator not in range [1,4]';

    const CHALLENGE_WINSIZE = 'Challenge window size not in range [1,5]';

    const ECI_VALUE = 'Invalid ECI value';

    const INVALID_HOSTED_FIELD_COLUMNS = 'Invalid hosted field columns';
}
