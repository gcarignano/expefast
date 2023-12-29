<?php
/**
 * Copyright 2023 Connfido BV. All rights reserved.
 * Distribution of this software without explicit written consent from Newgen is
 * strictly prohibited. No part of this software must not be reverse engineered,
 * copied, reproduced or modified.
 */

namespace Gateway\Common;

/**
 * Fields used for generating Payload
 *
 * @abstract
 */
abstract class Fields
{
    const FIRSTNAME = 'firstName';

    const LASTNAME = 'lastName';

    const EMAILID = 'emailId';

    const MOBILENO = 'mobileNo';

    const ADDRESSLINE1 = 'addressLine1';

    const ADDRESSLINE2 = 'addressLine2';

    const CITY = 'city';

    const STATE = 'state';

    const COUNTRY = 'country';

    const REGION = 'state';

    const ZIP = 'zip';

    const BILLINGADDRESS = 'billingAddress';

    const SHIPPINGADDRESS = 'shippingAddress';

    const CUSTOMER = 'customer';

    const CUSTOMER_ID = 'customerID';

    const MERCHANT = 'merchant';

    const MERCHANT_ID = 'merchantID';

    const PAYMENT_MODE = 'paymentMode';

    const PAYMENT_DETAIL = 'paymentDetail';

    const CARDTYPE = 'cardType';

    const BANKCODE = 'bankCode';

    const ORDER_ID = 'orderId';

    const TXN = 'transaction';

    const TXN_REFERENCE = 'txnReference';

    const TXN_AMOUNT = 'txnAmount';

    const TXN_PAGETAG = 'pageTag';

    const CURRENCYCODE = 'currencyCode';

    const CURRENCY = 'currency';

    const URL = 'url';

    const URL_SUCCESS = 'successURL';

    const URL_CANCEL = 'cancelURL';

    const URL_FAIL = 'failURL';

    const URL_CART = 'cartURL';

    const URL_PRIVACY = 'privacyURL';

    const URL_TERMS = 'termsURL';

    const URL_PRODUCT = 'productURL';

    const CONFIRMATIONPAGE = 'showConfirmationPage';

    const BIC = 'bic';

    const IBAN = 'iban';

    const BANK_HOLDER = 'holder';

    const CARD = 'card';

    const CARD_TOKEN = 'tokenID';

    const CARD_NUMBER = 'cardNumber';

    const CARD_YEAR = 'expYear';

    const CARD_MONTH = 'expMonth';

    const CARD_HOLDER = 'nameOnCard';

    const CARD_CVV = 'cvv';

    const CARD_SAVE = 'saveDetails';

    const CARD_BIN = 'bin';

    const CARD_LAST4 = 'last4';

    const CARD_ENC_NUMBER = 'encCardNumber';

    const CARD_ENC_ALGO = 'encAlgorithm';

    const CARD_ENC_KEYSEQ = 'keySequenceNumber';

    const TOKEN = 'token';

    const ACQUIRER = 'acquirer';

    const ACQUIRER_TOKEN = 'acquirerToken';

    const SUMMARY = 'summary';

    const ITEM = 'items';

    const ITEM_NAME = 'itemName';

    const ITEM_PRICE = 'itemPricePerUnit';

    const ITEM_QTY = 'itemQuantity';

    const ITEM_SKU = 'itemId';

    const REPORTTYPE = 'reportType';

    const SORTORDER = 'sortOrder';

    const SORTCOLUMN = 'sortColumn';

    const TODATE = 'toDate';

    const FROMDATE = 'fromDate';

    const FILTER = 'filters';

    const FILTER_FIELD = 'fieldName';

    const FILTER_VALUE = 'fieldValue';

    const LOCALE = 'locale';

    const DETAILS = 'details';

    const SUBTOTAL = 'subtotal';

    const TAX = 'tax';

    const SHIPPINGPRICE = 'shippingCharges';

    const DISCOUNT = 'discount';

    const DISCOUNT_AMOUNT = 'discountValue';

    const COUPON_CODE = 'couponCode';

    const COUPON_DESC = 'couponCodeDetails';

    const TXN_CAPTURE = 'capture';

    const TXN_VOID = 'void';

    const LANGUAGE = 'lang';

    const GATEWAY_REFERENCE = 'gatewayReference';

    const REFUND = 'refund';

    const REFUND_STATUS = 'refundStatus';

    const REFUND_AMOUNT = 'refundAmount';

    const COMMENTS = 'comments';

    const REFUND_INVOICE = 'refundInvoiceNo';

    const DATE_FROM = 'fromDate';

    const DATE_TO = 'toDate';

    const LIMIT = 'limit';

    const FROM_COUNT = 'fromCount';

    const SHOW_CUSTOM_DATA = 'showCustomData';

    const CUSTOM_DATA = 'customData';

    const CUSTOM_DATA1 = 'customData1';

    const CUSTOM_DATA2 = 'customData2';

    const CUSTOM_DATA3 = 'customData3';

    const CUSTOM_DATA4 = 'customData4';

    const CUSTOM_DATA5 = 'customData5';

    const SITE = 'site';

    const COMPANY_ID = 'registrationID';

    const COMPANY_AMOUNT = 'amount';

    const SHOW_ALL_CARDS = 'showAllCards';

    const WEBHOOK = 'webhook';

    const WEBHOOK_ID = 'webhookId';

    const STATUS = 'status';

    const EVENTS = 'events';

    const ASYNC = 'async';

    const SYNC = 'sync';

    const PAYOUT = 'payout';

    const HOSTED_PAGE = 'hostedPage';

    const CUSTOMER_EMAIL = 'customerEmail';

    const ALLOW3D = 'allow3D';

    const IFRAME = 'iFrame';

    const PAYMENTLINK = 'paymentLink';

    const PAYMENTLINK_DESCRIPTION = 'paymentLinkDescription';

    const BILLSHIP = 'allowBillShip';

    const NOTIFICATION_CHANNELS = 'notificationChannels';

    const CHANNEL_NAME = 'name';

    const CHANNEL_VALUE = 'value';

    const DD = 'dynamicDescriptor';

    const DD_NAME = 'name';

    const DD_EMAIL = 'email';

    const DD_MOBILE = 'mobile';

    const SUB_ID = 'subscriptionId';

    const SUB_AMOUNT = 'amount';

    const SUB_PERIOD = 'period';

    const SUB_FREQ = 'frequency';

    const SUB_INSTALLMENTS = 'installments';

    const SUB_INSTALLMENTS_TOTAL = 'totalInstallments';

    const SUB_TYPE = 'type';

    const SUB_SEQUENCE = 'sequence';

    const SUB_NAME = 'name';

    const SUB_CODE = 'code';

    const SUB_DESCRIPTION = 'description';

    const SUB_CARRY_FWD_AMOUNT = 'carryForwardAmount';

    const SUB_PAYMENT_FAIL_THRESHOLD = 'paymentFailureThreshold';

    const SUB_PLAN_ID = 'planId';

    const SUB_AUTOMATIC_DEBIT = 'automaticDebit';

    const SUB_START_DATE = 'startDate';

    const SUB_EXPIRY = 'expireIn';

    const SUB_QTY = 'quantity';

    const IS_APP = 'isApp';

    const THREE_DS = '3DSecure';

    const FINGERPRINT = 'deviceFingerprint';

    const TIMEZONE = "timezone";

    const BROWSERCOLORDEPTH = "browserColorDepth";

    const BROWSERLANGUAGE = "browserLanguage";

    const BROWSERSCREENHEIGHT = "browserScreenHeight";

    const BROWSERSCREENWIDTH = "browserScreenWidth";

    const OS = "os";

    const BROWSERACCEPTHEADER = "browserAcceptHeader";

    const USERAGENT = "userAgent";

    const BROWSERJAVASCRIPTENABLED = "browserJavascriptEnabled";

    const BROWSERJAVAENABLED = "browserJavaEnabled" ;

    const ACCEPTCONTENT = "acceptContent";

    const BROWSERIP = "browserIP";

    const EXEMPTIONS = "exemptions";

    const LOWVALUE = "lowValue";

    const TRA = "tra";

    const TRUSTEDBENEFICIARY = "trustedBeneficiary";

    const SECURECORPORATEPAYMENT = "secureCorporatePayment";

    const RECURRING_EXEMPTION_OTHER = "recurringMITExemptionOther";

    const RECURRING_EXEMPTION_SAMEAMOUNT = "recurringMITExemptionSameAmount";

    const DELEGATEDAUTHENTICATION = "delegatedAuthentication";

    const VMID = "vmid";

    const CHALLENGEWINDOWSIZE = "challengeWindowSize";

    const CHALLENGEINDICATOR = "challengeIndicator";

    const EXTERNALTHREEDS = "externalThreeds";

    const ECICODE = "eciCode";

    const THREEDSSTATUS = "threedsStatus";

    const ACSTRANSACTIONID = "acsTransactionId";

    const DSTRANSACTIONID = "dsTransactionId";

    const THREEDSSERVERTRANSACTIONID = "threedsServerTransactionId";

    const THREEDSVERSION = "threedsVersion";

    const AUTHENTICATIONVALUE = "authenticationValue";

    const XID = "xid";

    const SDK = "sdk";

    const SDKAPPID = "sdkAppID";

    const SDKENCDATA = "sdkEncData";

    const SDKEPHEMPUBKEY = "sdkEphemPubKey";

    const SDKMAXTIMEOUT = "sdkMaxTimeout";

    const SDKREFERENCENUMBER = "sdkReferenceNumber";

    const SDKTRANSID = "sdkTransID";

    const REGISTRATIONID = "registrationId";

    const WIRETRANSFER_DEFAULT = 'default';

    const WIRETANSFER_ISEEA = 'isEEA';

    const WIRETRANSFER_DESCRIPTION = 'description';

    const WIRETRANSFER_ACCOUNT_HOLDER = 'accountHolder';

    const WIRETRANSFER_BANK_ACCOUNT_HOLDER_NAME='name';

    const WIRETRANSFER_ACCOUNT_HOLDER_ADDRESS = 'address';

    const WIRETRANSFER_BANK = 'bank';

    const WIRETRANSFER_BANK_ADDRESS = 'address';

    const WIRETRANSFER_BANK_NAME = 'name';

    const  WIRETRANSFER_BANK_ACCOUNT_NUMBER = 'accountNumber';

    const WIRETRANSFER_BANK_SORT_CODE = 'sortCode';

    const SUB_EXTERNAL = 'external';

    const EXECUTION_DATE = 'executionDate';

    const HOSTED_FIELD_COLUMNS = 'columns';

    const REQUEST_PAYER_DETAILS = 'requestPayerDetails';

    const DOMAIN = 'domain';

    const PAYMENT_TOKEN = 'paymentToken';

    const X_CLIENT_IP = 'x-client-ip';

    const IP_ADDRESS = 'ipAddress';

    const DOB = 'dob';

    const DOCUMENT_ID = 'documentId';

    const DOCUMENT_TYPE = 'documentType';

    const GOOGLE_PAY = 'GOOGLE_PAY';

    const APPLE_PAY = 'APPLE_PAY';

    const CUSTOMER_MOBILE = 'customerMobile';
}
