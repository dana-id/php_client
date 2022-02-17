DANA PHP-CLIENT
===============

[DANA](https://dana.id) :heart: PHP!

This is the Official PHP wrapper/library for DANA Payment API, that is compatible with Composer.

## 1. Installation

### 1.1 Composer Installation

If you are using [Composer](https://getcomposer.org), you can install via composer CLI:

```
composer require dana-id/php_client
```

**or**

add this require line to your `composer.json` file:

```json
{
    "require": {
        "dana-id/php_client": "1.*"
    }
}
```

and run `composer install` on your terminal.

> **Note:** If you are using Laravel framework, in [some](https://laracasts.com/discuss/channels/general-discussion/using-non-laravel-composer-package-with-laravel?page=1#reply=461608) [case](https://stackoverflow.com/a/23675376) you also need to run `composer dumpautoload`

> `/DANA` will then be available (auto loaded) as Object in your Laravel project.

### 1.2 Manual Instalation

If you are not using Composer, you can clone or [download](https://github.com/dana-id/php_client/archive/refs/heads/main.zip) this repository.

Then you should require/autoload `DANA.php` file on your code.

```php
require_once dirname(__FILE__) . '/pathofproject/DANA.php';

// my code goes here
```

## 2. How to Use

### 2.1 General Settings

```php

// True: Production, False: Sandbox
\DANA\Config::$isProduction = false;

// True: if merchant use Mock API and vice versa
\DANA\Config::$isMockApi = false;

// Type of response that will be occured
\DANA\Config::$isMockScene = ''

// Obtain web URL, it depends on the environment have been chosen
\DANA\Config::$getWebUrl

// Obtain API URL, it depends on the environment have been chosen
\DANA\Config::$getApiUrl

// Obtain the value from onboarding process
\DANA\Config::$clientId = <put your clientId>;

// Obtain the value from onboarding process
\DANA\Config::$clientSecret = <put your clientSecret>;

// Obtain the value from onboarding process
\DANA\Config::$merchantId = <put your merchantId>;

// This private key will be configured on DANA backend
\DANA\Config::$privateKey = <put your privateKey>;

// As a link to define the URL that will be targeted when finish oauth process
\DANA\Config::$oauthRedirectUrl = <put your oauthRedirectUrl>;

// Merchant Category Code to define category of merchant and will be provided by DANA. This merchantMcc will be used in createOrder api
\DANA\Config::$merchantMcc = <put your merchantMcc>;

// As a link to define the URL that will be targeted when user finish doing payment in Cashier Page. If this value is empty, DANA will fetch the value from merchant onboarding process
\DANA\Config::$acquirementPayReturnUrl = <put your acquirementPayReturnUrl>;

// As a link for DANA Backend to send notification after the transaction has been finished in DANA. If this value is empty, DANA will fetch the value from merchant onboarding process
\DANA\Config::$acquirementNotificationUrl = <put your acquirementNotificationUrl>;

// Choose a strategy for checkout either GUEST_CHECKOUT or NORMAL_CHECKOUT. GUEST_CHECKOUT means user checkout their transaction without binding their account NORMAL_CHECKOUT means user have to bind their DANA account for using DANA on their transaction
\DANA\Config::$checkoutStrategy = <put your checkoutStrategy>;

// Define info of device that merchant used
\DANA\Config::$oauthTerminalType = <put your oauthTerminalType>;

// Determine which scope that provided by DANA. Default value: CASHIER,QUERY_BALANCE,DEFAULT_BASIC_PROFILE,MINI_DANA'
\DANA\Config::$oauthScopes = <put your oauthScopes>;

// Determine who’s actor has responsible to refund the transaction. Default value: SYSTEM
\DANA\Config::$refundActor = <put your refundActor>;

// Determine the destination for refund process. Default value: TO_BALANCE
\DANA\Config::$refundDestination = <put your refundDestination>;

```

### 2.2 Process Auth

#### generateLoginUrl

This login function provides users to login with DANA Account on the Merchant side. You have to request a URL with $clientId, $oauthScopes, and $oauthTerminalType that you’ve already set in the configuration process. After that, the function will ask for the user’s phone number, PIN, and consent. 

```php

$generateLoginUrl = \DANA\Auth::generateLoginUrl;

```

##### Result
When the process is already finished, DANA will generate an authCode, and the user browser will redirect to oauthRedirectUrl that you’ve already set on configuration config with authCode. The authCode is used to process the getAccessToken function.

#### generateSeamlessLoginUrl

This function is one of the OAuth flow processes. The difference between generateLoginUrl and generateSeamlessLoginUrl is the process of the input phone number and user’s consent. In generateSeamlessLoginUrl user does not have to input a phone number since the phone number is passed in the URL and does not have to put their consent. To use this function, you have to provide the seamlessData and seamlessSign in the Oauth URL.

```php

// Mandatory, variable string, User’s phone number
$phoneNo = '082*********';

// Mandatory, variable string, User’s userId on your system
$userId = '12345';

// Mandatory, variable string, DateTime of the user verified using OTP on your system, Format : '2022-02-16T02:52:30+00:00'
$verifiedTime = \DANA\Utils::getDateNow();

// Mandatory, variable string, CSRF protection, can be generated by either merchant or DANA.
$state = 'fea83d64-6803-4ff6-ab58-ec7efd78434d';

$generateSeamlessLoginUrl = \DANA\Auth::generateSeamlessLoginUrl($phoneNo, $userId, $verifiedTime, $state);

```

##### Result
When the process is already finished, DANA will generate an authCode and the user browser will redirect to oauthRedirectUrl that you’ve already set on configuration config with authCode. The authCode is used to process the getAccessToken function.

```json
  https::/merchantRedirecturl?authCode=123456&state=test
```

#### getAccessToken

After you have an authCode, you are able to obtain an Access Token by calling this function.


```php
// folowing redirect response after finish binding
$authCode = '123456';

$getAccessToken = \DANA\Auth::getAccessToken($authCode);

```

##### Result
When the process is complete and successful, DANA will return the accessToken, and you must keep and save accessToken. This token is required to be used for calling other DANA APIs. Meanwhile, when the process occurs an error, DANA will return resultInfo. 
The detailed resultInfo describes several parameters, please refer to the Result Code section for the list of result codes that might appear.

#### getUserProfile

This function is possible for you to obtain DANA’s user information. Before calling this function, make sure you’ve already gained the accessToken from the OAuth flow process either from generateLoginUrl or generateSeamlessLoginUrl.

```php
$accessToken = 'accessToken';

$getUserProfile = \DANA\Auth::getUserProfile($accessToken);

```

```json

{
  "result": {
    "balance": "1718636",
    "ott": "jasfhasohdfahfdiadfj12314qwndfagajndjfnasdfnasndgfjasdfahgkdashfgladbrkbanrdasdasdad",
    "phoneMasked": "62-*******9100",
    "topupUrl": "{baseurl}/m/portal/topup?ott=jasfhasohdfahfdiadfj12314qwndfagajndjfnasdfnasndgfjasdfahgkdashfgladbrkbanrdasdasdad",
    "minidanaUrl": "{baseurl}/m/ipg?sourcePlatform=IPG?sourcePlatform=IPG&ott=jasfhasohdfahfdiadfj12314qwndfagajndjfnasdfnasndgfjasdfahgkdashfgladbrkbanrdasdasdad"
  },
  "success": true
}

```


### 2.3 Process Transaction

#### agreementPay
The first process for transactions is to create an order, and you have to call this function for creating an order that the user has already done. This process only focuses on creating your order and doesn’t start the payment processing. The transaction process for a user who has already bound their DANA account, can directly to DANA’s Cashier page and this process is called Agreement Pay Order. To use this function you have to provide accessToken.

```php
// expiryTime use DANA utils
$expiryTime = \DANA\Utils::getDateAddDays();
$transactionType = 'MERCHANT_TRANS_TYPE';
$title = 'ORDER_20211013073454_209';
$orderId = 'MERCHANT_TRANS_ID_20211013073454_240';
$amountInCent = '100000';
$accessToken = 'accessToken';

$agreementPay = \DANA\Transaction::agreementPay($expiryTime, $transactionType, $title, $orderId, $amountInCent, $accessToken);

```

##### Result

When the agreementPay process is complete, DANA will return resultInfo, several following paramaters, and redirect to DANA checkout page (web HTML). 

```json

{
  "resultInfo": {
    "resultStatus": "S",
    "resultCodeId": "00000000",
    "resultMsg": "SUCCESS",
    "resultCode": "SUCCESS"
  },
  "merchantTransId": "MERCHANT_TRANS_ID_202110130223344_240",
  "acquirementId": "20211029111212800100166754201836302",
  "orderAmount": {
    "value": "100000",
    "currency": "IDR"
  },
  "createdTime": "2021-10-29T13:41:04+07:00",
  "paidTime": "2021-10-29T13:41:05+07:00",
  "fundChannelInfos": [
    {
      "payMethod": "BALANCE",
      "amount": {
        "value": "100000",
        "currency": "IDR"
      },
      "fundChannelDetails": []
    }
  ]
}

```

#### createOrder
This function is one of the creation order processes. The difference between agreementPay and createOrder is the process of agreemenPay is used to Bind and Pay solution meanwhile, createOrder is used to Guest Checkout Solution. 

```php
// expiryTime use DANA utils
$expiryTime = \DANA\Utils::getDateAddDays();
$transactionType = 'MERCHANT_TRANS_TYPE';
$title = 'ORDER_20211013073454_209';
$orderId = 'MERCHANT_TRANS_ID_20211013073454_240';
$amountInCent = '10000';

$createOrder = \DANA\Transaction::createOrder($expiryTime, $transactionType, $title, $orderId, $amountInCent);

```

##### Result
When the createOrder process is complete, DANA will return checkoutUrl, acquirementId, resultInfo, and redirect either to DANA login page.

Meanwhile, when the process occurs an error, DANA will return resultInfo. The detailed resultInfo describes several parameters, please refer to the Result Code section for the list of result codes that might appear when the process is either successful or not.

```json

{
  "acquirementId": "20211025111212800110166317000996653",
  "checkoutUrl": "{baseUrl}/m/portal/cashier/checkout?bizNo=12345555555&amp;timestamp=1635154385212&amp;originSourcePlatform=IPG&amp;mid=12345&amp;sign=GGPE3G49k7n8iYClOOwebm6qIl07IW0igvQdkK%2BVvJR%2BMpoHZ4vXR2UEJM%2FR2A6rhNP2nA5k4tQwEVocrRk3BYSN7VVcdnjgNj0LsPu4kbIVQmfR7CXi4LUAqJF%2B%2FbgT2ilP33Xe4tgmJcBkNJGDJCTMp1bG%2Ba72DCU2WjLijLFflE2jkT9kbYDLwFKqR8e8pkrg%2BZrBSiNzmF9vYN%2Bsfze8s7IteJpgJZ2VWioUj2n7tpSjd%2F8SjjrJE3E47ITCRofpoosMZUqUU694xw2%2FGT7uT6Nkbm668MlIky0A%2F3%2Bx0SyJbjV7efqLWbCnIiTMu17qivp091mSI6lpMG2Y%2Bw%3D%3D",
  "resultInfo": {
    "resultStatus": "S",
    "resultCodeId": "00000000",
    "resultMsg": "SUCCESS",
    "resultCode": "SUCCESS"
  }
} 

```

#### queryOrder
This function provides detailed information for each order.

```php
$orderId = 'MERCHANT_TRANS_ID_20211013073454_240';
$acquirementId = '20211025111212800110166317000996653';
$accessToken = 'accessToken';

$queryOrder = \DANA\Transaction::queryOrder($orderId, $acquirementId, $accessToken);

```

##### Result
When the queryOrder process is complete, DANA will return all of the information related to the order, please refer to the response sample-success section to know what’s the information provided by DANA.

Meanwhile, when the process occurs an error, DANA will return resultInfo. The detailed resultInfo describes several parameters, please refer to the Result Code section for the list of result codes that might appear.

```json

{
  "statusDetail": {
    "frozen": false,
    "acquirementStatus": "INIT"
  },
  "timeDetail": {
    "expiryTime": "2021-10-26T16:36:00+07:00",
    "createdTime": "2021-10-25T16:36:00+07:00"
  },
  "orderTitle": "ORDER_20211013073454_209",
  "resultInfo": {
    "resultCode": "SUCCESS",
    "resultStatus": "S",
    "resultMsg": "SUCCESS",
    "resultCodeId": "00000000"
  },
  "acquirementId": "20211025111212800110166389600993012",
  "merchantTransId": "MERCHANT_TRANS_ID_20211013073454_222",
  "buyer": {
    "userId": "216610000000000038382"
  },
  "amountDetail": {
    "orderAmount": {
      "value": "100",
      "currency": "IDR"
    }
  },
  "goods": [
    {
      "price": {
        "value": "100",
        "currency": "IDR"
      },
      "description": "ORDER_20211013073454_209",
      "extendInfo": "{'objectId':'','objectCode':'','objectOperator':''}"
    }
  ]
}

```

#### cancelOrder
After the payment process has been done and the result did not return a clear result, you are allowed to cancel the order transaction. Once the cancelOrder is completed, you could safely assume as if the transaction never happened.

```php
$acquirementId = '20211025111212800110166389600993012';
$merchantTransId = 'MERCHANT_TRANS_ID_20211013073454_222';
$cancelReason = 'cancel';
$accessToken = 'accessToken';

$cancelOrder = \DANA\Transaction::cancelOrder($acquirementId, $merchantTransId, $cancelReason, $accessToken);
```

##### Result
When the cancelOrder process is complete, DANA will return cancelledTime. Meanwhile, when the process occurs an error, DANA will return resultInfo. The detailed resultInfo describes several parameters, please refer to the Result Code section for the list of result codes that might appear, you shall use this field to decide what’s the next action to proceed. 

```json
"2021-10-25T16:39:12+07:00"
```

#### refundOrder
After the payment process has been done, you are allowed to refund an order transaction as long as there is still has transaction amount. A transaction can be refunded partially or fully, and both of them don’t change the transaction status.

```php
// requestId use DANA utils
$requestId = \DANA\Utils::generateGuid();
$acquirementId = '20211025111212800110166389600993012';
$amountInCent = '1000';
$reason = 'refund';
$merchantUserId = 'merchantUserId';
$accessToken = 'accessToken';

$refundOrder = \DANA\Transaction::refundOrder($requestId, $acquirementId, $amountInCent, $reason,  $merchantUserId,$accessToken);
```

##### Result
When the refundOrder process is complete and successful, DANA will return $refundId. Meanwhile, when the process occurs an error, DANA will return resultInfo. The detailed resultInfo describes several parameters, please refer to the Result Code section for the list of result codes that might appear.

```json
20211025111212801300166690900256483
```

#### queryRefund
This function provides detailed information for each refund order transaction. 

```php
$refundId = '20211025111212801300166690900256483';
$acquirementId = '20211025111212800110166389600993012';
$merchantTransId = 'MERCHANT_TRANS_ID_20211013073454_222';
$accessToken = 'accessToken';

$queryRefund = \DANA\Transaction::queryRefund($refundId, $acquirementId, $merchantTransId, $accessToken);
```

##### Result
When the queryRefund process is complete and successful, DANA will return refundInfos model. Meanwhile, when the process occurs an error, DANA will return resultInfo. The detailed resultInfo describes several parameters, please refer to the Result Code section for the list of result codes that might appear.

```json
[
  {
    "refundId": "20211025111212801300166690900256483",
    "requestId": "92814d7e-1622-42b7-a574-cacfc9840cdd",
    "refundAmount": {
      "value": "100000",
      "currency": "IDR"
    },
    "refundReason": "refund",
    "refundAcceptedTime": "2021-10-25T17:08:13+07:00",
    "refundedTime": "2021-10-25T17:08:13+07:00",
    "refundToUserTime": "2021-10-25T17:08:13+07:00",
    "actorType": "SYSTEM",
    "returnChargeToPayer": "false",
    "refundStatus": "SUCCESS",
    "refundChannelDetails": [
      {
        "fundProcessId": "2021102510110000049000DANAW3ID166695001581036",
        "refundMethod": "SYSTEM",
        "refundChannel": "BALANCE",
        "refundAmount": {
          "value": "100000",
          "currency": "IDR"
        },
        "refundedTime": "2021-10-25T17:08:13+07:00",
        "success": "true"
      }
    ],
    "extendInfo": "{}"
  }
]
```

### 2.4 SPI
#### finishNotify
After the transaction order process is finished, DANA will notify the transaction status of either success or failure to config::acquirementNotificationUrl that you have set on configuration process. 

```php
$merchantTransId = 'MERCHANT_TRANS_ID_20211013073454_222';
$acquirementId = '20211025111212800110166389600993012';
$acquirementStatus = 'FINISH';
$orderAmount = '1000';

// createdTime use DANA utils
$createdTime = \DANA\Utils::getDateNow();

// finishedTime use DANA utils
$finishedTime = \DANA\Utils::getDateAddDays();

$finishNotify = \DANA\Spi::finishNotify($merchantTransId, $acquirementId, $acquirementStatus, $orderAmount, $createdTime, $finishedTime);
```

##### Result
When the process is complete, the result will appear Result Code either the process is successful or not and will redirect to config::acquirementPayReturnUrl as you have set on configuration process.

```json
{
  "response":{
      "head":{
          "version":"2.0",
          "function":"dana.acquiring.order.finishNotify",
          "clientId":"PAYTW3IN51",
          "respTime":"2001-07-04T12:08:56+05:30",
          "reqMsgId":"1234567asdfasdf1123fda"
      },
      "body":{
          "resultInfo":{
              "resultStatus":"S",
              "resultCodeId":"00000000",
              "resultCode":"SUCCESS",
              "resultMsg":"success"
          }
      }
  },
  "signature":"signature string"
}
```
