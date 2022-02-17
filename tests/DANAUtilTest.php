<?php

namespace DANA;

use Datetime;
use DateInterval;

class DANAUtilTest extends \PHPUnit\Framework\TestCase
{
  public function testGenerateGuid()
  {
    $generateGuid1 = Util::generateGuid();
    $generateGuid2 = Util::generateGuid();
    $this->assertIsString($generateGuid1);
    $this->assertIsString($generateGuid2);
    $this->assertNotEquals($generateGuid1, $generateGuid2);
  }

  public function testGetDateNow()
  {
    $getDateNow = Util::getDateNow();
    $dateNow = date(Config::DANA_DATE_FORMAT);
    $this->assertEquals($getDateNow, $dateNow);
  }

  public function testDateAddMinute()
  {
    $n = 30;
    $now = new DateTime();
    $now->modify(sprintf('+%d minute', $n));
    $dateNow = $now->format(Config::DANA_DATE_FORMAT);
    $dateAddMinute = Util::dateAddMinute($n);
    $this->assertEquals($dateAddMinute, $dateNow);
  }

  public function testGetDateAddDays()
  {
    $days = 2;
    $now = new DateTime();
    $now->add(new DateInterval('P' . $days . 'D'));
    $dateNow = $now->format(Config::DANA_DATE_FORMAT);
    $dateAddDays = Util::getDateAddDays($days);
    $this->assertEquals($dateAddDays, $dateNow);
  }

  public function testLogError()
  {
    $logError = Util::logError('test error');
    $this->assertEquals($logError, '');
  }

  public function testGetArrayData()
  {
    $testArray = ['test', 'test1', 'test2', ''];

    $array = Util::getArrayData(1, 2);
    $this->assertEquals($array, '');

    $array = Util::getArrayData($testArray, 4);
    $this->assertEquals($array, '');

    $array = Util::getArrayData($testArray, 0);
    $this->assertEquals($array, 'test');

    $array = Util::getArrayData($testArray, 3, 'testArray');
    $this->assertEquals($array, 'testArray');
  }

  public function testIsValidPhone()
  {
    $isValidPhone = Util::isValidPhone('082342877421');
    $this->assertEquals($isValidPhone, true);

    $isValidPhone1 = Util::isValidPhone('062+82342877421');
    $this->assertEquals($isValidPhone1, true);

    $isValidPhone2 = Util::isValidPhone('+6282342877421');
    $this->assertEquals($isValidPhone2, false);
  }

  public function testSanitizePhone()
  {
    $sanitizePhone = Util::sanitizePhone('082342877412abc');
    $number = '082342877412';
    $this->assertEquals($sanitizePhone, $number);
  }

  public function testSignature()
  {
    $privateKey = <<<EOD
    -----BEGIN PRIVATE KEY-----
    MIIEvgIBADANBgkqhkiG9w0BAQEFAASCBKgwggSkAgEAAoIBAQDnLOT7AWiMIgsO
    c2gt/ZwDucGGs6giMpxrZGLECMuGgvLx75WNxrPhFIii/pA7owbzd1/ye+Oh3t0l
    R+BLbd7V+iyQUZiFxTfmfdDPnEKwqrTzSbbyynZhNr/gPKs9jrVjOnyQ/4b/CQj4
    YXFDY7ErCQPT9UAL9tqvNKsgXTY4uGtx3WKfBPHc72WxqRXftMKgAjlukkrODGE7
    jxmFobKZTWkgUGVjIzfV47im/VeBHhUQoeFxXMLMQC72i7dYg/aDEIFlS8EJIsw0
    7MuevUFI5ANEBpfiFkYloLDMSd0PY/jDaFNVHQmhRbEEApmBqadf9MJozqvflFz1
    WdTFwAGXAgMBAAECggEBAMIS+40KCqYScmvIUT7C+XZbPPAD5XQIPy8dUJk4tPyQ
    9LpNo6UpmpusLGWCBr46SDyfDCq6/UhInCmPJOrOK1RCOaVTCxPKlPpox6NzkL1W
    IqFYUkGT3TwRxZXP+QatxvOuByOHkeKLyD8VNwwXJoZ1hAQukX052S9brQp3lmyY
    NgNY4pV5FgIV8gSsgxyPrYB//Y3bhmR4bVizevHjDEtw6uvwD2LTka/tpYiwP40k
    +BFykI81ML4Sf9rK7C6glK9zNI1Bd/TD8f0yBV2+ix9SjVHZICtO9MdEgTeaOOxm
    DX400S4BKk5pDinjYJiIWuOTeyHTfP6eDyl5lAj4zoECgYEA9pQ71jGt7ua20t3G
    ckrGTTPbuNuoFYvB9hxMOeiJZvxYEcy8aBI2Trj286W231E6uyBXS2Z6XKozZRBo
    zYVlCUhtJkRB9lvU991u4+yFr+rbzFCwixiYAfVYcGOywcpEIirbDHx4oBB6AVKG
    a/4Tot57q6nVSM3aNWaALCBOFNcCgYEA8AH/twvd9V/aKjoMIog9PyBiYqM5GiMS
    AJ7TtQc7rOrwzFR2VIbTRUC358ZbK1ZZ/H3i+XVP64gRs9e6LjYFJd+wCQQ6J0KS
    G5nRxFqBRC9cJ8LwWJAUfZ+ZCc5KyV25fPOBjK7bdIZ25dXMeddaY2OHlYhQLwjy
    YuT6McQLIUECgYB43GGT9JfXoJh+NRw/Cy21y7RoIKp7nRw+QNKQE829f/S/DMZQ
    kJSz82+AL3q6bTtHW2vOVnWlk/tLD0b/beH/MdPmTNC0K4Dw2UWwTE+e0ZRYyjgu
    haiEVTi7JfMJj9XjlXP248/QTSMwIL4oksoXK8wccUtMuzG4uPwcJN2A0wKBgQCz
    trt0o/UqlAB3WAnYHa3GxAgHlfLfCF2li0g5KFZd73opdiE4v9AY7hIHAjcoJzuw
    Xc8EPfx+/99JjAKEMbz/FBTrW3f0B9wBwNcasS5UESZvO3/ewNwnCMd+WTMUvxJy
    Zp+d6Ry4jysehE3c+g1bmJ5gsLZh0dA1jwFtHD+9QQKBgBdnTdLu145UExPJ4RGa
    Y8AibYPSzmKROs0VaIEBCWi3+anfD9P8eZ3dZ3+L4Y54IVloopeGu2+tNKPAwLQg
    9l3Kr9eyZDaSldkBJVmN+Fh5StNnob9afM79NsKyR5QPgFZmgc6t2WSbgdKNsWIE
    4F0pIoj/wbYIvEY8si7Q/I3s
    -----END PRIVATE KEY-----
    EOD;
    $publicKey = <<<EOD
    -----BEGIN PUBLIC KEY-----
    MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA5yzk+wFojCILDnNoLf2c
    A7nBhrOoIjKca2RixAjLhoLy8e+Vjcaz4RSIov6QO6MG83df8nvjod7dJUfgS23e
    1foskFGYhcU35n3Qz5xCsKq080m28sp2YTa/4DyrPY61Yzp8kP+G/wkI+GFxQ2Ox
    KwkD0/VAC/barzSrIF02OLhrcd1inwTx3O9lsakV37TCoAI5bpJKzgxhO48ZhaGy
    mU1pIFBlYyM31eO4pv1XgR4VEKHhcVzCzEAu9ou3WIP2gxCBZUvBCSLMNOzLnr1B
    SOQDRAaX4hZGJaCwzEndD2P4w2hTVR0JoUWxBAKZgamnX/TCaM6r35Rc9VnUxcAB
    lwIDAQAB
    -----END PUBLIC KEY-----
    EOD;
    $seamlessData = array(
      'mobile'       => 'test',
    );
    $seamlessDataText  = json_encode($seamlessData, JSON_UNESCAPED_SLASHES);
    $signature = Util::generateSignature($seamlessDataText, $privateKey);
    $verifySignature = Util::verifySignature($seamlessDataText, $signature, $publicKey);
    $this->assertEquals($verifySignature, true);
  }

  public function testPayloadSignature()
  {
    $privateKey = <<<EOD
    -----BEGIN PRIVATE KEY-----
    MIIEvgIBADANBgkqhkiG9w0BAQEFAASCBKgwggSkAgEAAoIBAQDnLOT7AWiMIgsO
    c2gt/ZwDucGGs6giMpxrZGLECMuGgvLx75WNxrPhFIii/pA7owbzd1/ye+Oh3t0l
    R+BLbd7V+iyQUZiFxTfmfdDPnEKwqrTzSbbyynZhNr/gPKs9jrVjOnyQ/4b/CQj4
    YXFDY7ErCQPT9UAL9tqvNKsgXTY4uGtx3WKfBPHc72WxqRXftMKgAjlukkrODGE7
    jxmFobKZTWkgUGVjIzfV47im/VeBHhUQoeFxXMLMQC72i7dYg/aDEIFlS8EJIsw0
    7MuevUFI5ANEBpfiFkYloLDMSd0PY/jDaFNVHQmhRbEEApmBqadf9MJozqvflFz1
    WdTFwAGXAgMBAAECggEBAMIS+40KCqYScmvIUT7C+XZbPPAD5XQIPy8dUJk4tPyQ
    9LpNo6UpmpusLGWCBr46SDyfDCq6/UhInCmPJOrOK1RCOaVTCxPKlPpox6NzkL1W
    IqFYUkGT3TwRxZXP+QatxvOuByOHkeKLyD8VNwwXJoZ1hAQukX052S9brQp3lmyY
    NgNY4pV5FgIV8gSsgxyPrYB//Y3bhmR4bVizevHjDEtw6uvwD2LTka/tpYiwP40k
    +BFykI81ML4Sf9rK7C6glK9zNI1Bd/TD8f0yBV2+ix9SjVHZICtO9MdEgTeaOOxm
    DX400S4BKk5pDinjYJiIWuOTeyHTfP6eDyl5lAj4zoECgYEA9pQ71jGt7ua20t3G
    ckrGTTPbuNuoFYvB9hxMOeiJZvxYEcy8aBI2Trj286W231E6uyBXS2Z6XKozZRBo
    zYVlCUhtJkRB9lvU991u4+yFr+rbzFCwixiYAfVYcGOywcpEIirbDHx4oBB6AVKG
    a/4Tot57q6nVSM3aNWaALCBOFNcCgYEA8AH/twvd9V/aKjoMIog9PyBiYqM5GiMS
    AJ7TtQc7rOrwzFR2VIbTRUC358ZbK1ZZ/H3i+XVP64gRs9e6LjYFJd+wCQQ6J0KS
    G5nRxFqBRC9cJ8LwWJAUfZ+ZCc5KyV25fPOBjK7bdIZ25dXMeddaY2OHlYhQLwjy
    YuT6McQLIUECgYB43GGT9JfXoJh+NRw/Cy21y7RoIKp7nRw+QNKQE829f/S/DMZQ
    kJSz82+AL3q6bTtHW2vOVnWlk/tLD0b/beH/MdPmTNC0K4Dw2UWwTE+e0ZRYyjgu
    haiEVTi7JfMJj9XjlXP248/QTSMwIL4oksoXK8wccUtMuzG4uPwcJN2A0wKBgQCz
    trt0o/UqlAB3WAnYHa3GxAgHlfLfCF2li0g5KFZd73opdiE4v9AY7hIHAjcoJzuw
    Xc8EPfx+/99JjAKEMbz/FBTrW3f0B9wBwNcasS5UESZvO3/ewNwnCMd+WTMUvxJy
    Zp+d6Ry4jysehE3c+g1bmJ5gsLZh0dA1jwFtHD+9QQKBgBdnTdLu145UExPJ4RGa
    Y8AibYPSzmKROs0VaIEBCWi3+anfD9P8eZ3dZ3+L4Y54IVloopeGu2+tNKPAwLQg
    9l3Kr9eyZDaSldkBJVmN+Fh5StNnob9afM79NsKyR5QPgFZmgc6t2WSbgdKNsWIE
    4F0pIoj/wbYIvEY8si7Q/I3s
    -----END PRIVATE KEY-----
    EOD;
    $publicKey = <<<EOD
    -----BEGIN PUBLIC KEY-----
    MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA5yzk+wFojCILDnNoLf2c
    A7nBhrOoIjKca2RixAjLhoLy8e+Vjcaz4RSIov6QO6MG83df8nvjod7dJUfgS23e
    1foskFGYhcU35n3Qz5xCsKq080m28sp2YTa/4DyrPY61Yzp8kP+G/wkI+GFxQ2Ox
    KwkD0/VAC/barzSrIF02OLhrcd1inwTx3O9lsakV37TCoAI5bpJKzgxhO48ZhaGy
    mU1pIFBlYyM31eO4pv1XgR4VEKHhcVzCzEAu9ou3WIP2gxCBZUvBCSLMNOzLnr1B
    SOQDRAaX4hZGJaCwzEndD2P4w2hTVR0JoUWxBAKZgamnX/TCaM6r35Rc9VnUxcAB
    lwIDAQAB
    -----END PUBLIC KEY-----
    EOD;
    $requestData = [
      'head' => [
        'version'      => '2.0',
      ],
      'body' => [
        'userResources' => ['BALANCE', 'OTT', 'TRANSACTION_URL', 'MASK_DANA_ID', 'TOPUP_URL']
      ]
    ];
    $requestDataText = json_encode($requestData, JSON_UNESCAPED_SLASHES);
    $signature = Util::generateSignature($requestDataText, $privateKey);

    $responsePayload = [
      'response'  => $requestData,
      'signature' => $signature
    ];
    $responsePayloadText = json_encode($responsePayload, JSON_UNESCAPED_SLASHES);
    $verifyPayloadSignature = Util::verifyPayloadSignature($responsePayloadText, $publicKey);
    $this->assertEquals($verifyPayloadSignature, true);
  }

  public function testIsValidTime()
  {
    $getDateNow = strtotime('2012-01-18T');
    $validTime = Util::isValidTime($getDateNow);
    $this->assertEquals($validTime, false);
    $isNotValidTime = Util::isValidTime('danadompetdigital');
    $this->assertEquals($isNotValidTime, false);
  }

  public function testComposeNotifyResponse()
  {
    $composeNotifyResponse = Util::composeNotifyResponse([]);
    $this->assertEquals($composeNotifyResponse, '{"response":[],"signature":"s55joyNOsZBfojb6i4A3kCZItVmpNi/N+scdNWH8BYhinjNhIvRJ9PGWCPcrNWIl9mzWYYA+r9a4oN+Dx17Yv4Lig6+3WCSxSqwNTml/eKT++FCoJVnkrA7pVOHRXKzgEKTD0dDwTtKmeEmHTpVsoa76u6vFbBJoUN5fK8xBNxHgvEhyxI9xzPwyg4Akbhlt7Rbb1FwRE54BdIZgUY3bD6TJqPwFrimrQ1LoN4qB6VHi7dnafJ5DRLPLd58ANvEChwBqMtOq+gNr+jpBrovx3y/Hg4acPX2bDl/ui+YrEf2h5DM77xiKBP3+WSmervkcHDr7/Pm9ZGKJyZD2HyMahA=="}');
  }

  public function testComposeRequest()
  {
    $composeRequest = Util::composeRequest([]);
    $this->assertEquals($composeRequest, '{"request":[],"signature":"s55joyNOsZBfojb6i4A3kCZItVmpNi/N+scdNWH8BYhinjNhIvRJ9PGWCPcrNWIl9mzWYYA+r9a4oN+Dx17Yv4Lig6+3WCSxSqwNTml/eKT++FCoJVnkrA7pVOHRXKzgEKTD0dDwTtKmeEmHTpVsoa76u6vFbBJoUN5fK8xBNxHgvEhyxI9xzPwyg4Akbhlt7Rbb1FwRE54BdIZgUY3bD6TJqPwFrimrQ1LoN4qB6VHi7dnafJ5DRLPLd58ANvEChwBqMtOq+gNr+jpBrovx3y/Hg4acPX2bDl/ui+YrEf2h5DM77xiKBP3+WSmervkcHDr7/Pm9ZGKJyZD2HyMahA=="}');
  }

  public function testDanaApi()
  {
    $url = '/dana/acquiring/order/cancel.htm';
    $requestData = [
      'head' => [
          'version'      => '2.0',
          'function'     => 'dana.acquiring.order.cancel',
          'clientId'     => Config::$clientId,
          'clientSecret' => Config::$clientSecret,
          'reqTime'      => Util::getDateNow(),
          'reqMsgId'     => Util::generateGuid(),
          'accessToken'  => '',
          'reserve'      => '{}',
        ],
        'body' => [
          'acquirementId'   => '1234',
          'merchantTransId' => '1234',
          'cancelReason'    => 'cancel',
          'merchantId'      => Config::$merchantId,
        ]
    ];
    $response = Util::danaApi($url, $requestData);
    $response = json_decode($response, true);
    if (Util::isResponseSuccess($response, 'SUCCESS')) {
      return $response['response']['body']['cancelTime'];
    }
    $resultInfo = $response['response']['body']['resultInfo'];
    $this->assertContains('SYSTEM_ERROR', $resultInfo);
  }

  public function testValidateStatement()
  {
    try {
      $statement = Util::validateStatement(true, 'error');
    } catch (\Exception $error) {
      $this->assertEquals($error->getMessage(), 'error');
    }
  }

  public function testIsResponseSuccess()
  {
    $response = '';
    $isResponseSuccess = Util::isResponseSuccess($response);
    $this->assertEquals($isResponseSuccess, false);

    $response = [
      'response' => [
        'head' => [
          'version'      => '2.0',
        ],
        'body' => [
          'resultInfo' => [
            'resultCode' => "SUCCESS",
          ]
        ]
      ]
    ];
    $isResponseSuccess = Util::isResponseSuccess($response);
    $this->assertEquals($isResponseSuccess, true);
  }
}