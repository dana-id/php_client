<?php

namespace DANA;

class DANASpiTest extends \PHPUnit\Framework\TestCase
{
    public function testFinishNotify()
    {
      Config::$isProduction = false;
      $this->assertEquals(NULL, Spi::finishNotify(
        '200000', 
        '200000', 
        'SUCCESS', 
        [
          'currency' => 'IDR',
          'value' => '300000'
        ], 
        '2015-07-04T12:08:56+05:30', 
        '2015-07-04T12:08:56+05:30'
      ));
    }
}