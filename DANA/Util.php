<?php

namespace DANA;

use Exception;
use Datetime;
use DateInterval;

class Util
{
  /**
   * Generate random GUID
   */
  public static function generateGuid()
  {
      if (function_exists('com_create_guid') === true)
          return trim(com_create_guid(), '{}');

      $data    = openssl_random_pseudo_bytes(16);
      $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
      $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10

      return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
  }

  /**
   * Get today's date in ISO-8601, ie. 2001-07-04T12:08:56+05:30
   * Make sure you set timezone correctly
   *
   * @return string DateTime in ISO-8601
   */
  public static function getDateNow()
  {
      return date(Config::DANA_DATE_FORMAT);
  }

  /**
   * Get today's date + $n minutes
   * This utility can be used for order.expiryTime, where you want the order to be now+$n minutes.
   * Make sure you set your timezone correctly
   *
   * @param $n integer minutes
   * @return string DateTime in ISO-8601
   *
   * @throws Exception date time error
   */
  public static function dateAddMinute($n = 1)
  {
      $now = new DateTime();
      $now->modify(sprintf('+%d minute', $n));

      return $now->format(Config::DANA_DATE_FORMAT);
  }

  /**
   * Get today's date + $n days
   * This utility can be used for order.expiryTime, where you want the order to be now+$days days.
   * Make sure you set your timezone correctly
   *
   * @param $days integer days
   * @return string DateTime in ISO-8601
   *
   * @throws Exception date time error
   */
  public static function getDateAddDays($days = 1)
  {
      $date = new DateTime();
      $date->add(new DateInterval('P' . $days . 'D'));

      return $date->format(Config::DANA_DATE_FORMAT);
  }

  /**
   * Verify json payload's signature against given $publicKey
   *
   * @param $payloadText string json payload
   * @param $publicKey string public key
   *
   * @return bool true/false, if given payload's signature is valid, it will return true.
   */
  public static function verifyPayloadSignature($payloadText, $publicKey)
  {
      $firstOffset = strpos($payloadText, '{"head"');
      $lastOffset  = strpos($payloadText, ',"signature"');
      $body        = substr($payloadText, $firstOffset, $lastOffset - $firstOffset);

      $payloadObject   = json_decode($payloadText, true);
      $signatureBase64 = $payloadObject['signature'];

      return self::verifySignature($body, $signatureBase64, $publicKey);
  }

  /**
   * Generate signature for given $data string, using $privateKey key
   *
   * @param $data string json payload that need to be signed
   * @param $privateKey string, PKCS#8 private key
   *
   * @return string base64 encoded signature
   */
  public static function generateSignature($data, $privateKey)
  {
      $signature = '';
      openssl_sign($data, $signature, $privateKey, OPENSSL_ALGO_SHA256);

      return base64_encode($signature);
  }

  /**
   * Verify signature, openssl
   *
   * @param $data string json payload
   * @param $signatureBase64 string, signature in base64
   * @param $publicKey string, public key
   *
   * @return boolean
   */
  public static function verifySignature($data, $signatureBase64, $publicKey)
  {
      $binarySignature = base64_decode($signatureBase64);

      return (bool)openssl_verify($data, $binarySignature, $publicKey, OPENSSL_ALGO_SHA256);
  }

  /**
   * Handy utility to log error
   * You can add your log channel here
   */
  public static function logError($message)
  {
      error_log($message);
  }

  /**
   * Extract $array[$key]
   *
   * @param $array array, haystack
   * @param $key string, needle key
   * @param $default string, default return value if $array does not have $key
   *
   * @return mixed
   */
  public static function getArrayData($array, $key, $default = '')
  {
      if (!is_array($array)) {
          return '';
      }

      if (!isset($array[$key])) {
          return '';
      }

      if (empty($array[$key])) {
          return $default;
      }

      return $array[$key];
  }

  /**
   * Validate dateTime string
   * @param $dateTime string, date in ISO-8601 (non-Z)
   *
   * @return bool
   */
  public static function isValidTime($dateTime)
  {
      if (preg_match('/^' .
              '(\d{4})-(\d{2})-(\d{2})T' . // YYYY-MM-DDT ex: 2014-01-01T
              '(\d{2}):(\d{2}):(\d{2})' .  // HH-MM-SS  ex: 17:00:00
              '((-|\+)\d{2}:\d{2})' .  //+01:00 or -01:00
              '$/', $dateTime, $parts) == true) {
          try {
              new DateTime($dateTime);

              return true;
          } catch (Exception $e) {
              return false;
          }
      } else {
          return false;
      }
  }

  /**
   * Validate $phone based on DANA standard, it should be:
   * - 08XXXXXXXXX
   * - 062+8XXXXXXXX
   *
   * @param $phone string phone no
   *
   * @return bool
   */
  public static function isValidPhone($phone)
  {
      if (strpos($phone, '062+8') === 0 || strpos($phone, '08') === 0) {
          return true;
      }

      return false;
  }

  /**
   * Remove unwanted character in $phone
   *
   * @param $phone string, unsanitized phone no
   *
   * @return string, sanitized phone without spaces/whitespaces
   */
  public static function sanitizePhone($phone)
  {
      $phone = preg_replace('/\s/', '', $phone);

      if (strpos($phone, '062+8') === 0) {
          $suffix    = substr($phone, strlen('062+8'));
          $sanitized = '062+8' . preg_replace('/\D/', '', $suffix);
      } else {
          $sanitized = preg_replace('/\D/', '', $phone);
      }

      return $sanitized;
  }

  /**
   * Compose notify response for ACK
   *
   * @param $responseData array response that need to be composed
   *
   * @return string json payload with embedded signature
   */
  public static function composeNotifyResponse($responseData)
  {
      // convert 'null' into ''
      array_walk_recursive($responseData, function (&$item) {
          $item = strval($item);
      });

      $responseDataText = json_encode($responseData, JSON_UNESCAPED_SLASHES);
      $signature        = self::generateSignature($responseDataText, Config::$privateKey);

      $responsePayload = [
          'response'  => $responseData,
          'signature' => $signature
      ];

      return json_encode($responsePayload, JSON_UNESCAPED_SLASHES);
  }

  /**
   * Compose request for api call, embed signature
   *
   * @param $requestData array paylaod that need to be composed
   *
   * @return string json payload with embedded signature
   */
  public static function composeRequest($requestData)
  {
      // convert 'null' into ''
      array_walk_recursive($requestData, function (&$item) {
          $item = strval($item);
      });

      $requestDataText = json_encode($requestData, JSON_UNESCAPED_SLASHES);
      $requestDataText = preg_replace('/\\\\\\\"/',"\"", $requestDataText); // remove unnecessary double escape
      $signature       = self::generateSignature($requestDataText, Config::$privateKey);

      $requestPayload = [
          'request'   => $requestData,
          'signature' => $signature
      ];

      $requestPayloadText = json_encode($requestPayload, JSON_UNESCAPED_SLASHES);
      $requestPayloadText = preg_replace('/\\\\\\\"/',"\"", $requestPayloadText); // remove unnecessary double escape

      return $requestPayloadText;
  }

  /**
   * Main api function to call to DANA
   *
   * @param $url string, the path of api, without domain name
   * @param $payloadObject array, object that need to be sent to $url
   *
   * @return string response payload
   */
  public static function danaApi($url, $payloadObject)
  {
      $isMockApi = Config::$isMockApi;
      $isMockScene = Config::$isMockScene;
      
      $jsonPayload = self::composeRequest($payloadObject);

      $curl = curl_init();
      $opts = [
          CURLOPT_URL            => Config::getApiUrl() . $url,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING       => "",
          CURLOPT_MAXREDIRS      => 10,
          CURLOPT_TIMEOUT        => 30,
          CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST  => "POST",
          CURLOPT_POSTFIELDS     => $jsonPayload,
          CURLOPT_HTTPHEADER     => [
              "Content-Type: application/json",
              "Cache-control: no-cache",
              "X-DANA-SDK: PHP",
              "X-DANA-SDK-VERSION: 1.0",
          ]
      ];

      curl_setopt_array($curl, $opts);

      if (!isset($isMockApi) || $isMockApi == false) {
          $response = curl_exec($curl);
          $err      = curl_error($curl);

          curl_close($curl);

          if ($err) {
              die("cURL Error #:" . $err);
          }
      } else {
          $response = file_get_contents(ROOT_DIR . '/tests/mocks/' . $isMockScene . '.json');
      }

      return $response;
  }

  /**
   * Validate boolean statement, if valid will throw exception
   */
  public static function validateStatement($statement, $thrownMessage) {
      if ($statement) {
          throw new Exception($thrownMessage);
      }
  }


  /**
   * Check whether given $response array is success response or no
   *
   * @param $response array response
   * @param $expectedResultInfoCode string, expected resultInfo.resultCode value to be determined as success
   *
   * @return bool
   */
  public static function isResponseSuccess($response, $expectedResultInfoCode = 'SUCCESS') {
      if (!is_array($response)) {
          return false;
      }

      if (!isset($response['response'])
          || !isset($response['response']['body'])
          || !isset($response['response']['body']['resultInfo'])
          || !isset($response['response']['body']['resultInfo']['resultCode'])
          ) {

          return false;
      }

      return $response['response']['body']['resultInfo']['resultCode'] == $expectedResultInfoCode;
  }
}