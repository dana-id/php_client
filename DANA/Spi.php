<?php

namespace DANA;

class Spi
{
  /*
    $payload => [
      'acquirementId' => '...', (M)
      'merchantTransId' => '...', (M)
      'subscriptionId' => '...',
      'finishedTime' => '2015-07-04T12:08:56+05:30', (M)
      'createdTime' => '2015-07-04T12:08:56+05:30', (M)
      'merchantId' => '...', (M)
      'orderAmount' => [...], (M)
      'acquirementStatus' => 'CLOSED || SUCCESS' (M)
    ];
  */
  public static function finishNotify (
    $merchantTransId,
    $acquirementId,
    $acquirementStatus,
    $orderAmount,
    $createdTime,
    $finishedTime
  ) {
    Util::validateStatement(empty($merchantTransId), "merchantTransId must not be empty!");
    Util::validateStatement(empty($acquirementId), "acquirementId must not be empty!");
    Util::validateStatement(empty($acquirementStatus), "acquirementStatus must not be empty!");
    Util::validateStatement(empty($orderAmount), "orderAmount must not be empty!");
    Util::validateStatement(empty($createdTime), "createdTime must not be empty!");
    Util::validateStatement(empty($finishedTime), "finishedTime must not be empty!");

    $requestData = [
      'head' => [
        'version'      => '2.0',
        'function'     => 'dana.acquiring.order.finishNotify',
        'clientId'     => Config::$clientId,
        'reqTime'      => Util::getDateNow(),
        'reqMsgId'     => Util::generateGuid(),
      ],
      'body' => [
        'merchantId'      => Config::$merchantId,
        'merchantTransId' => $merchantTransId,
        'acquirementId'   => $acquirementId,
        'acquirementStatus'   => $acquirementStatus,
        'orderAmount'   => $orderAmount,
        'createdTime'   => $createdTime,
        'finishedTime'   => $finishedTime,
      ]
    ];

    $response = Util::danaApi('/dana/acquiring/order/finishNotify.htm', $requestData);

    try {
      $result = json_decode($response, true);
      if (isset($result)) return $result['response']['body']['resultInfo'];
    } catch (Exception $e) {
      return [
        "success" => false,
        "status" => 500,
        "result" => $e,
      ];
    }
  }
}