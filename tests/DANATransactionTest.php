<?php

namespace DANA;

class DANATransactionTest extends \PHPUnit\Framework\TestCase
{
  public function testCancelOrderEmptyParameter()
  {
    try {
        $cancelOrderData = Transaction::cancelOrder();
    } catch (\Exception $error) {
        $this->assertEquals('acquirementId or merchantTransId must exist as parameter', $error->getMessage());
    }
  }

  public function testCancelOrder()
  {
    Config::$isProduction = false;
    $cancelOrderData = Transaction::cancelOrder(123456, 123456, 'wrong');
    $this->assertContains('SYSTEM_ERROR', $cancelOrderData);
  }
  
  public function testRefundOrder()
  {
    Config::$isProduction = false;
    $this->assertEquals(
      [
        'resultCode' => 'OAUTH_FAILED',
        'resultCodeId' => '00000016',
        'resultStatus' => 'F',
        'resultMsg' => 'OAUTH_FAILED'
      ], 
      Transaction::refundOrder(
        '200000', 
        '200000', 
        '',
        '',
        '20000'
      )
    );
  }

  public function testQueryRefund()
  {
    Config::$isProduction = false;
    $this->assertEquals(
      [
        'resultCode' => 'OAUTH_FAILED',
        'resultCodeId' => '00000016',
        'resultStatus' => 'F',
        'resultMsg' => 'OAUTH_FAILED'
      ], 
      Transaction::queryRefund(
        '200000', 
        '200000', 
        '20000'
      )
    );
  }

  public function testCreateOrder()
  {
    Config::$isProduction = false;
    $this->assertEquals(
      [
        'resultInfo' => [
          'resultStatus' => 'F',
          'resultCodeId' => '12005001',
          'resultMsg' => 'merchantContract is not exists, productCode=[51051000100000000001],merchantId=[216620000000140414598]',
          'resultCode' => 'ACCESS_DENIED'
        ]
      ], 
      Transaction::createOrder(
        '2022-10-08T03:00:10+00:00', 
        'transactionType', 
        'title',
        'orderId',
        '2000000',
        'riskObjectId',
        'riskObjectCode',
        'riskObjectOperator'
      )
    );
  }

  public function testAgreementPay()
  {
    Config::$isProduction = false;
    $this->assertEquals(
      [
        'resultInfo' => [
          'resultCode' => 'OAUTH_FAILED',
          'resultCodeId' => '00000016',
          'resultStatus' => 'F',
          'resultMsg' => 'OAUTH_FAILED'
        ]
      ], 
      Transaction::agreementPay(
        '2022-10-08T03:00:10+00:00', 
        'transactionType', 
        'title',
        'orderId',
        '2000000',
        '1234556666',
      )
    );
  }

  public function testQueryOrder()
  {
    Config::$isProduction = false;
    $this->assertEquals(
      [
        'resultCode' => 'SYSTEM_ERROR',
        'resultCodeId' => '00000900',
        'resultStatus' => 'U',
        'resultMsg' => 'SYSTEM_ERROR'
      ], 
      Transaction::queryOrder(
        'orderId',
        'acquirementId'
      )
    );
  }
}