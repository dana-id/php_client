<?php

namespace DANA;

use Exception;

class Transaction
{
  /**
   *
   * Cancel order.
   * This API is used by merchant to cancel a transaction when the ‘pay’ API call did NOT return a clear result,
   * for example, when the ‘pay’ API call timeout or SYSTEM_ERROR is returned.
   * Upon the success of cancel API call, merchant could safely assume as if the transaction never happened.
   *
   * @param $acquirementId string, acquirementId from createOrder api response
   * @param $merchantTransId string, your order ID, the same value that you use for parameter in createOrder api
   * @param $cancelReason string, for audit reason, you can add descriptive notes here
   *
   * @return mixed if success, will return cancelTime. If some error happened, will return $resultInfo
   *
   * @throws Exception if both acquirementId and merchantTransId empty
   */

  /*
    ResultInfo References:
    ~~~~~~~~~~~~~~~~~~~~~
    No	ResultCodeId	ResultCode	                    ResultStatus	Remarks
    1	  00000000	    SUCCESS	                        S	            success
    2	  00000004	    PARAM_ILLEGAL	                  F	            parameter illegal
    3	  00000900	    SYSTEM_ERROR	                  U	            unknown system error
    4	  12005002	    ORDER_NOT_EXISTS	              F	            order is not exist.
    5	  12005100	    ORDER_IS_CLOSED	                F	            order status is closed.
    6	  12005004	    ORDER_IS_FROZEN	                F	            order status is frozen.
    7	  12003001	    MERCHANT_NOT_EXIST	            F	            merchant not exist
    8	  12005200	    MERCHANT_STATUS_ABNORMAL	      F	            merchant status abnormal
    9	  12005109	    USER_NOT_EXISTS	                F	            user not exist
    10	12005110	    USER_STATUS_ABNORMAL	          F	            user status invalid
    11	22005201	    BALANCE_NOT_ENOUGH	            F	            balance is not enough
    12	22005204	    ACCOUNT_STATUS_ABNORMAL	        F	            Dana account is invalid.
    13	12005500	    AGREEMENT_CANCEL_NOT_ALLOWED	  F	            cancel not allowed by agreement
    14	12005501	    CANCEL_EXPIRED	                F	            out of the cancel expiry time
    15	12005502	    ORDER_HAS_BEEN_DISPUTED	        F	            order has been disputed before, no matter success or failed
    16	12005503	    ORDER_HAS_BEEN_REFUNDED	        F	            order has been refunded before, no matter success or failed
    17	12005504	    ORDER_HAS_BEEN_VOIDED	          F	            order has been voided before, no matter success or failed
    18	12005505	    ORDER_HAS_BEEN_CONFIRMED	      F	            order has been confirmed by user or captured by merchant before, no matter success or failed
    19	12005506	    ORDER_HAS_BEEN_ACCEPTED	        F	            order has been accepted by merchant
    20	00000019	    PROCESS_FAIL	                  F	            process fail, but suggest merchant retry.
  */
  
  public static function cancelOrder($acquirementId = '', $merchantTransId = '', $cancelReason = '', $accessToken = '')
  {
    if (empty($acquirementId) && empty($merchantTransId)) {
      throw new Exception('acquirementId or merchantTransId must exist as parameter');
    }

    $requestData = [
      'head' => [
          'version'      => '2.0',
          'function'     => 'dana.acquiring.order.cancel',
          'clientId'     => Config::$clientId,
          'clientSecret' => Config::$clientSecret,
          'reqTime'      => Util::getDateNow(),
          'reqMsgId'     => Util::generateGuid(),
          'accessToken'  => $accessToken ? $accessToken : '',
          'reserve'      => '{}',
        ],
        'body' => [
          'acquirementId'   => $acquirementId,
          'merchantTransId' => $merchantTransId,
          'cancelReason'    => $cancelReason,
          'merchantId'      => Config::$merchantId,
        ]
    ];

    $response = Util::danaApi('/dana/acquiring/order/cancel.htm', $requestData);

    $result = [];
    try {
      $response = json_decode($response, true);

      if (Util::isResponseSuccess($response, 'SUCCESS')) {
        return $response['response']['body']['cancelledTime'];
      }
      $resultInfo = $response['response']['body']['resultInfo'];
      
      return $resultInfo;
    } catch (Exception $e) {
      throw new Exception('Internal Errors');
    }
    return $result;
  }

  public static function refundOrder (
    $requestId, 
    $acquirementId, 
    $amountInCent = '100', 
    $reason = 'refund',
    $accessToken = ''
  ) {
    Util::validateStatement(empty($requestId), 'requestId must not be empty!');
    Util::validateStatement(empty($acquirementId), 'acquirementId must not be empty!');

    $requestData = [
      'head' => [
        'version'      => '2.0',
        'function'     => 'dana.acquiring.refund.refund',
        'clientId'     => Config::$clientId,
        'clientSecret' => Config::$clientSecret,
        'reqTime'      => Util::getDateNow(),
        'reqMsgId'     => Util::generateGuid(),
        'accessToken'  => $accessToken ? $accessToken : '',
        'reserve'      => '{}',
      ],
      'body' => [
        'requestId'  => $requestId,
        'merchantId' => Config::$merchantId,
        'acquirementId' => $acquirementId,
        'refundAmount'  => [
            'value'    => $amountInCent,
            'currency' => 'IDR'
        ],
        'refundAppliedTime'      => Util::getDateNow(),
        'actorType' => Config::$refundActor,
        'refundReason' => $reason,
        'destination' => Config::$refundDestination,
        'extendInfo' => '{}',
        'envInfo'          => [
          'terminalType'       => 'SYSTEM',
          'osType'             => '',
          'extendInfo'         => '',
          'orderOsType'        => '',
          'sdkVersion'         => '',
          'websiteLanguage'    => '',
          'tokenId'            => '',
          'sessionId'          => '',
          'appVersion'         => '',
          'merchantAppVersion' => '',
          'clientKey'          => '',
          'orderTerminalType'  => 'SYSTEM',
          'clientIp'           => '',
          'sourcePlatform'     => 'IPG'
        ]
      ]
    ];

    $response = Util::danaApi('/dana/acquiring/refund/refund.htm', $requestData);

    try {
      $response      = json_decode($response, true);
      $resultInfo    = $response['response']['body']['resultInfo'];

      return $resultInfo['resultCode'] == 'ACCEPTED_SUCCESS' ? 
      $response['response']['body']['refundId'] : 
      $resultInfo;
    } catch (Exception $e) {
      return [
        'success' => false,
        'status' => 500,
        'result' => $e,
      ];
    }
  }

  public static function queryRefund (
    $refundId, 
    $acquirementId = '', 
    $merchantTransId = '',
    $accessToken = ''
  ) {
    $requestData = [
      'head' => [
        'version'      => '2.0',
        'function'     => 'dana.acquiring.refund.query',
        'clientId'     => Config::$clientId,
        'clientSecret' => Config::$clientSecret,
        'reqTime'      => Util::getDateNow(),
        'reqMsgId'     => Util::generateGuid(),
        'accessToken'  => $accessToken ? $accessToken : '',
        'reserve'      => '{}',
      ],
      'body' => [
        'merchantId'      => Config::$merchantId,
        'refundId'        => $refundId,
        'merchantTransId' => $merchantTransId,
        'acquirementId'   => $acquirementId
      ]
    ];

    $response = Util::danaApi('/dana/acquiring/refund/query.htm', $requestData);

    try {
      $result = json_decode($response, true);
      if (Util::isResponseSuccess($result)) {
        return $result['response']['body']['refundInfos'];
      }

      return $result['response']['body']['resultInfo'];
    } catch (Exception $e) {
      return [
        'success' => false,
        'status' => 500,
        'result' => $e,
      ];
    }
  }

  public static function createOrder (
    $expiryTime,
    $transactionType,
    $title,
    $orderId,
    $amountInCent,
    $riskObjectId,
    $riskObjectCode,
    $riskObjectOperator,
    $accessToken = ''
    ) {
      $requestData = [
        'head' => [
          'version'      => '2.0',
          'function'     => 'dana.acquiring.order.createOrder',
          'clientId'     => Config::$clientId,
          'clientSecret' => Config::$clientSecret,
          'reqTime'      => Util::getDateNow(),
          'reqMsgId'     => Util::generateGuid(),
          'accessToken'  => $accessToken ? $accessToken : '',
          'reserve'      => '{}',
        ],
        'body' => [
          'envInfo'          => [
            'terminalType'       => 'SYSTEM',
            'osType'             => '',
            'extendInfo'         => '',
            'orderOsType'        => '',
            'sdkVersion'         => '',
            'websiteLanguage'    => '',
            'tokenId'            => '',
            'sessionId'          => '',
            'appVersion'         => '',
            'merchantAppVersion' => '',
            'clientKey'          => '',
            'orderTerminalType'  => 'SYSTEM',
            'clientIp'           => '',
            'sourcePlatform'     => 'IPG'
          ],
          'order'            => [
            'expiryTime'        => $expiryTime,
            'merchantTransType' => $transactionType,
            'orderTitle'        => $title,
            'merchantTransId'   => $orderId,
            'orderMemo'         => '',
            'createdTime'       => Util::getDateNow(),
            'orderAmount'       => [
              'value'    => $amountInCent,
              'currency' => 'IDR'
            ],
            'goods'             => [
              [
                'unit'               => '',
                'category'           => '',
                'price'              => [
                  'value'    => $amountInCent,
                  'currency' => 'IDR'
                ],
                'merchantShippingId' => '',
                'merchantGoodsId'    => '',
                'description'        => $title,
                'snapshotUrl'        => '',
                'quantity'           => '',
                'extendInfo'         => '{\'objectId\':\'' . $riskObjectId . '\',\'objectCode\':\'' . $riskObjectCode . '\',\'objectOperator\':\'' . $riskObjectOperator . '\'}'
              ]
            ]
          ],
          'productCode'      => '51051000100000000001',
          'mcc'              => Config::$merchantMcc,
          'merchantId'       => Config::$merchantId,
          'extendInfo'       => '',
          'notificationUrls' => [
            [
              'type' => 'PAY_RETURN',
              'url'  => Config::$acquirementPayReturnUrl
            ],
            [
              'type' => 'NOTIFICATION',
              'url'  => Config::$acquirementNotificationUrl
            ],
          ]
        ]
      ];

      $response = Util::danaApi('/dana/acquiring/order/createOrder.htm', $requestData);

    try {
      $response   = json_decode($response, true);
      $resultInfo = $response['response']['body']['resultInfo'];
      
      if (Util::isResponseSuccess($response)) {
        $acquirementId = $response['response']['body']['acquirementId'];
        $checkoutUrl   = htmlspecialchars($response['response']['body']['checkoutUrl']);

        $result = [
          'acquirementId' => $acquirementId,
          'checkoutUrl'   => $checkoutUrl,
          'resultInfo'    => $resultInfo
        ];

        return $result;
      }

      return $response['response']['body'];
    } catch (Exception $e) {
      return [
        'success' => false,
        'status' => 500,
        'result' => $e,
      ];
    }

    return null;
  }

  public static function agreementPay (
    $expiryTime,
    $transactionType,
    $title,
    $orderId,
    $amountInCent,
    $accessToken = ''
    ) {
      $requestData = [
        'head' => [
          'version'      => '2.0',
          'function'     => 'dana.acquiring.order.agreement.pay',
          'clientId'     => Config::$clientId,
          'clientSecret' => Config::$clientSecret,
          'reqTime'      => Util::getDateNow(),
          'reqMsgId'     => Util::generateGuid(),
          'accessToken'  => $accessToken ? $accessToken : '',
          'reserve'      => '{}',
        ],
        'body' => [
          'envInfo'          => [
            'terminalType'       => 'SYSTEM',
            'osType'             => '',
            'extendInfo'         => '',
            'orderOsType'        => '',
            'sdkVersion'         => '',
            'websiteLanguage'    => '',
            'tokenId'            => '',
            'sessionId'          => '',
            'appVersion'         => '',
            'merchantAppVersion' => '',
            'clientKey'          => '',
            'orderTerminalType'  => 'SYSTEM',
            'clientIp'           => '',
            'sourcePlatform'     => 'IPG'
          ],
          'order'            => [
            'expiryTime'        => $expiryTime,
            'merchantTransType' => $transactionType,
            'orderTitle'        => $title,
            'merchantTransId'   => $orderId,
            'orderMemo'         => '',
            'createdTime'       => Util::getDateNow(),
            'orderAmount'       => [
              'value'    => $amountInCent,
              'currency' => 'IDR'
            ],
            'goods'             => [
              [
                'unit'               => '',
                'category'           => '',
                'price'              => [
                  'value'    => $amountInCent,
                  'currency' => 'IDR'
                ],
                'merchantShippingId' => '',
                'merchantGoodsId'    => '',
                'description'        => $title,
                'snapshotUrl'        => '',
                'quantity'           => '',
                'extendInfo'         => ''
              ]
            ]
          ],
          'productCode'       => '51051000100000000031',
          'mcc'               => Config::$merchantMcc,
          'paymentPreference' => [
            'disabledPayMethods' => "OTC^VIRTUAL_ACCOUNT"
          ],
          'merchantId'        => Config::$merchantId,
          'extendInfo'        => '',
          'notificationUrls'  => [
            [
              'type' => 'NOTIFICATION',
              'url'  => Config::$acquirementNotificationUrl
            ],
          ]
        ]
      ];

      $response = Util::danaApi('/dana/acquiring/order/agreement/pay.htm', $requestData);

    try {
      $response   = json_decode($response, true);
      return $response['response']['body'];
    } catch (Exception $e) {
      return [
        'success' => false,
        'status' => 500,
        'result' => $e,
      ];
    }

    return null;
  }

  public static function queryOrder($orderId, $acquirementId, $accessToken = '')
  {
    $requestData = [
      'head' => [
        'version'      => '2.0',
        'function'     => 'dana.acquiring.order.query',
        'clientId'     => Config::$clientId,
        'clientSecret' => Config::$clientSecret,
        'reqTime'      => Util::getDateNow(),
        'reqMsgId'     => Util::generateGuid(),
        
        'accessToken'  => $accessToken ? $accessToken : '',
        'reserve'      => '{}',
      ],
      'body' => [
        'merchantId'      => Config::$merchantId,
        'merchantTransId' => $orderId,
        'acquirementId'   => $acquirementId,
        'extendInfo'      => '',
      ]
    ];

    $response = Util::danaApi('/dana/acquiring/order/query.htm', $requestData);

    try {
      $result = json_decode($response, true);
      if (Util::isResponseSuccess($result)) {
        return $result['response']['body'];
      }

      return $result['response']['body']['resultInfo'];
    } catch (Exception $e) {
      return [
        'success' => false,
        'status' => 500,
        'result' => $e,
      ];
    }

    return null;
  }
}