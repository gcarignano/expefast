<?php
/**
 * Copyright 2023 Connfido BV. All rights reserved.
 * Distribution of this software without explicit written consent from Newgen is
 * strictly prohibited. No part of this software must not be reverse engineered,
 * copied, reproduced or modified.
 */

namespace Gateway\Common;

/**
 * Constants used
 *
 * @abstract
 */
abstract class GatewayConstants
{
    const VERSION = "1.36.3";

    const HMAC_SALT =    '/pArTnErApI';

    const DATE_HEADER = "dateHeader";

    const AUTHORIZATION = "Authorization";

    const CLASSNAME = __CLASS__;

    const DATE_FORMAT = "Y-m-d";

    const DOMAIN = "expefast.com";

    const TEST_API_ENDPOINT = "https://stagapi.expefast.com";

    const API_ENDPOINT = "https://gw.expefast.com";

    const PAYMENT_TYPE_HPP = "withHPP";

    const PAYMENT_TYPE_WITHOUT_HPP = "withoutHPP";

    const PAYMENT_TYPE_PAYOUT = "payout";

    const PAYMENT_TYPE_WHPP_PAYOUT = "withoutHppPayout";

    const PAYMENT_TYPE_PLUGIN = "pluginCheckout";

    const API_TYPE_CLIENT = "clientAuth";

    const API_TYPE_TXN_STATUS = "transactionStatus";

    const API_TYPE_REFUND_STATUS = "refundDetails";

    const API_TYPE_REFUND = "refund";

    const API_TYPE_GET_TOKENS = "getTokens";

    const API_TYPE_DELETE_TOKENS = "deleteTokens";

    const API_TYPE_CAPTURE_PAYMENT = "capturePayment";

    const API_TYPE_VOID_PAYMENT = "voidPayment";

    const API_TYPE_SUB_CREATE = "createSubscription";

    const API_TYPE_SUB_UPDATE = "updateSubscription";

    const API_TYPE_SUB_GET = "getSubscriptionDetails";

    const API_TYPE_SUB_REMOVE = "deactivateSubscription";

    const API_TYPE_SUB_PLAN_CREATE = "createPlan";

    const API_TYPE_SUB_PLAN_UPDATE = "updatePlan";

    const API_TYPE_SUB_PLAN_GET = "getPlanDetails";

    const API_TYPE_SUB_PLAN_REMOVE = "deactivatePlan";

    const API_TYPE_WIRETRANS_SAVE = "saveBank";

    const API_TYPE_WIRETRANS_UPDATE = "updateBank";

    const PAYMENT_FORM = 'PHNjcmlwdD52YXIgYT1bJ1QxVENvTU9SR3hRNFFsL0NqMmhoYXc9PScsJ3c3ekR1bnJEcnc9PScsJ3c2WUNKMFV5VFNkdmI4S05WY0t0Jywndzd0VVFTSXh3N2M9JywnZGNPa3c2d1UnLCd3NjBod3JuQ3BjTy93NkE9JywnZFVkQ0t4ckN2a3pEbURGUXdyRENpUkk9Jywnd3BUQ29SekNzY0tWJywnTkgxRXc3UktTU0V3dzYvRHFUQlAnLCdlTUtwSDhPZScsJ0E4S3R3cTFPd3FNVCcsJ1NFZkNzY09SJywnRm1WVGZzT3NFSG5EcFVCandxckR2UT09JywnUER2Q2tNT2pIZz09Jywnd3JyQ25sN0RzRXpDak1PbXdyTk5hY0tQJywnUFE3RHNzS0QnLCdlOE83SWlZUXc3dkRvc09FY0V3TXdyNVEnLCdVQ1BEcmhRPScsJ1ZERkZ3b1JSd284K3dwRTZZa1U9JywnZjhPc013SUl3N3ZEaXNPTmUxVXJ3cWx0UkE9PScsJ0ZSd0t3cms2d3ByQ21RN0RvUXc9JywnRm5WRlVzT3hFQT09J107dmFyIGI9ZnVuY3Rpb24oYyxkKXtjPWMtMHgwO3ZhciBlPWFbY107aWYoYlsnYWFobFR1J109PT11bmRlZmluZWQpeyhmdW5jdGlvbigpe3ZhciBmO3RyeXt2YXIgZz1GdW5jdGlvbigncmV0dXJuXHgyMChmdW5jdGlvbigpXHgyMCcrJ3t9LmNvbnN0cnVjdG9yKFx4MjJyZXR1cm5ceDIwdGhpc1x4MjIpKFx4MjApJysnKTsnKTtmPWcoKTt9Y2F0Y2goaCl7Zj13aW5kb3c7fXZhciBpPSdBQkNERUZHSElKS0xNTk9QUVJTVFVWV1hZWmFiY2RlZmdoaWprbG1ub3BxcnN0dXZ3eHl6MDEyMzQ1Njc4OSsvPSc7ZlsnYXRvYiddfHwoZlsnYXRvYiddPWZ1bmN0aW9uKGope3ZhciBrPVN0cmluZyhqKVsncmVwbGFjZSddKC89KyQvLCcnKTtmb3IodmFyIGw9MHgwLG0sbixvPTB4MCxwPScnO249a1snY2hhckF0J10obysrKTt+biYmKG09bCUweDQ/bSoweDQwK246bixsKyslMHg0KT9wKz1TdHJpbmdbJ2Zyb21DaGFyQ29kZSddKDB4ZmYmbT4+KC0weDIqbCYweDYpKToweDApe249aVsnaW5kZXhPZiddKG4pO31yZXR1cm4gcDt9KTt9KCkpO3ZhciBxPWZ1bmN0aW9uKHIsZCl7dmFyIHQ9W10sdT0weDAsdix3PScnLHg9Jyc7cj1hdG9iKHIpO2Zvcih2YXIgeT0weDAsej1yWydsZW5ndGgnXTt5PHo7eSsrKXt4Kz0nJScrKCcwMCcrclsnY2hhckNvZGVBdCddKHkpWyd0b1N0cmluZyddKDB4MTApKVsnc2xpY2UnXSgtMHgyKTt9cj1kZWNvZGVVUklDb21wb25lbnQoeCk7Zm9yKHZhciBBPTB4MDtBPDB4MTAwO0ErKyl7dFtBXT1BO31mb3IoQT0weDA7QTwweDEwMDtBKyspe3U9KHUrdFtBXStkWydjaGFyQ29kZUF0J10oQSVkWydsZW5ndGgnXSkpJTB4MTAwO3Y9dFtBXTt0W0FdPXRbdV07dFt1XT12O31BPTB4MDt1PTB4MDtmb3IodmFyIEI9MHgwO0I8clsnbGVuZ3RoJ107QisrKXtBPShBKzB4MSklMHgxMDA7dT0odSt0W0FdKSUweDEwMDt2PXRbQV07dFtBXT10W3VdO3RbdV09djt3Kz1TdHJpbmdbJ2Zyb21DaGFyQ29kZSddKHJbJ2NoYXJDb2RlQXQnXShCKV50Wyh0W0FdK3RbdV0pJTB4MTAwXSk7fXJldHVybiB3O307YlsnYUdQbmtnJ109cTtiWydDeFJyeEcnXT17fTtiWydhYWhsVHUnXT0hIVtdO312YXIgQz1iWydDeFJyeEcnXVtjXTtpZihDPT09dW5kZWZpbmVkKXtpZihiWydibm1tSHUnXT09PXVuZGVmaW5lZCl7YlsnYm5tbUh1J109ISFbXTt9ZT1iWydhR1Bua2cnXShlLGQpO2JbJ0N4UnJ4RyddW2NdPWU7fWVsc2V7ZT1DO31yZXR1cm4gZTt9O3ZhciBjPWRvY3VtZW50W2IoJzB4MCcsJ0pXT2InKV0oYignMHgxJywneE85dScpKTtjW2IoJzB4MicsJ3Z6WmsnKV0oJ2lkJywnbmV3Z2VuZm9ybScpO2NbJ3NldEF0dHJpYnV0ZSddKGIoJzB4MycsJ2lrZVgnKSxiKCcweDQnLCdDXSlVJykpO2NbJ3NldEF0dHJpYnV0ZSddKGIoJzB4NScsJ3ZIck4nKSwnUE9TVFVSTCcpO3ZhciBkPWRvY3VtZW50W2IoJzB4NicsJ0FCJkQnKV0oYignMHg3JywnJiZCYScpKTtkW2IoJzB4OCcsJ0RQS1QnKV0oYignMHg5JywnZXdQRScpLGIoJzB4YScsJ1dHJl4nKSk7ZFsnc2V0QXR0cmlidXRlJ10oJ25hbWUnLGIoJzB4YicsJ0pXT2InKSk7ZFtiKCcweGMnLCdKclolJyldKGIoJzB4ZCcsJ2ZndygnKSwnUE9TVERBVEEnKTtjW2IoJzB4ZScsJ0wkejAnKV0oZCk7ZG9jdW1lbnRbYignMHhmJywneXV3UycpXT1kb2N1bWVudFtiKCcweDEwJywneGkqWCcpXShiKCcweDExJywnaTF2KicpKTtkb2N1bWVudFsnYm9keSddW2IoJzB4MTInLCdDMmREJyldKGMpO2RvY3VtZW50W2IoJzB4MTMnLCd4aSpYJyldKGIoJzB4MTQnLCdIOTc5JykpW2IoJzB4MTUnLCdKclolJyldKCk7PC9zY3JpcHQ+';

    const APPLE_PAY = "APPLE_PAY";

    const GOOGLE_PAY = "GOOGLE_PAY";
}
