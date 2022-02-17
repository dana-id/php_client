<?php

namespace DANA;

class Auth
{

/**
 * Get User Resources (profiles)
 *
 * @param $accessToken string, you get this value from dana.oauth.auth.applyToken api
 *
 * @return array user's profile
 * @return null if exception happened
 */

  public static function getUserProfile($accessToken) 
  {
    $requestData = [
      'head' => [
        'version'      => '2.0',
        'function'     => 'dana.member.query.queryUserProfile',
        'clientId'     => Config::$clientId,
        'clientSecret' => Config::$clientSecret,
        'reqTime'      => Util::getDateNow(),
        'reqMsgId'     => Util::generateGuid(),
        'accessToken'  => $accessToken,
        'reserve'      => '{}',
      ],
      'body' => [
        'userResources' => ['BALANCE', 'OTT', 'TRANSACTION_URL', 'MASK_DANA_ID', 'TOPUP_URL']
      ]
  ];

  $response = Util::danaApi("/dana/member/query/queryUserProfile.htm", $requestData);

  $result = '';
  try {
    $result = json_decode($response, true);
    if (!Util::isResponseSuccess($result)) {
      return $result['response']['body']['resultInfo'];
    }

    $resources = $result['response']['body']['userResourceInfos'];

    $resourcesMap = [];
    foreach ($resources as $resource) {
      $resourcesMap[$resource['resourceType']] = $resource['value'];
    }

    $balance     = $resourcesMap['BALANCE'];
    $ott         = $resourcesMap['OTT'];
    $phoneMasked = $resourcesMap['MASK_DANA_ID'];
    $topupUrl    = $resourcesMap['TOPUP_URL'] . '?ott=' . $ott;
    $minidanaUrl = $resourcesMap['TRANSACTION_URL'] . '?sourcePlatform=IPG&ott=' . $resourcesMap['OTT'];

    $result = [
      'balance'     => $balance,
      'ott'         => $ott,
      'phoneMasked' => $phoneMasked,
      'topupUrl'    => $topupUrl,
      'minidanaUrl' => $minidanaUrl
    ];
  } catch (Exception $e) {}

  return $result;
  }

  /**
   * @param $state string, CSRF protection, make sure you make it random enough to prevent Forgery Attack.
   *  if empty, will be auto generated using UUID
   *
   * @return string url of login
   */

  public static function generateLoginUrl($state = null)
  {
    if (empty($state)) {
      $state = Util::generateGuid();
    }
  
    $url = sprintf(
      '%s/d/portal/oauth?clientId=%s&scopes=%s&requestId=%s&state=%s&terminalType=%s&redirectUrl=%s',
      Config::getWebUrl(),
      Config::$clientId,
      Config::$oauthScopes,
      Util::generateGuid(),
      $state,
      Config::$oauthTerminalType,
      urlencode(Config::$oauthRedirectUrl)
    );
  
    return $url;
  }

  /**
   * @param $phoneNo string, valid phone no: 08XXXXXXX, don't use +628XXX
   * @param $userId string, UserID in your system
   * @param $verifiedTime string, DateTime of the user verified using OTP in your system.
   * @param $state string, CSRF protection
   *
   * @throws Exception when phoneNo or userId or verifiedTime cannot empty
   * @throws Exception when verifiedTime not valid
   *
   * @return string seamlessLogin url
   *
   */

  public static function generateSeamlessLoginUrl($phoneNo, $userId, $verifiedTime, $state = '')
  {
    if (empty($phoneNo) || empty($userId)) {
      throw new Exception("phoneNo or userId cannot empty");
    }

    $phoneNo = Util::sanitizePhone($phoneNo);
    if (!Util::isValidPhone($phoneNo)) {
      throw new Exception("phoneNo not valid");
    }

    if (empty($state)) {
      $state = Util::generateGuid();
    }

    $reqMsgId = Util::generateGuid();
    $reqTime  = Util::getDateNow();

    $seamlessData = array(
      'mobile'       => $phoneNo,
      'externalUid'  => $userId,
      'verifiedTime' => $verifiedTime,
      'reqMsgId'     => $reqMsgId,
      'reqTime'      => $reqTime
    );

    $seamlessDataText  = json_encode($seamlessData, JSON_UNESCAPED_SLASHES);
    $seamlessSignature = Util::generateSignature($seamlessDataText, Config::$privateKey);

    $url = sprintf('%s/d/portal/oauth?seamlessData=%s&seamlessSign=%s&clientId=%s&scopes=%s&requestId=%s&state=%s&terminalType=%s&redirectUrl=%s',
      Config::getWebUrl(),
      urlencode($seamlessDataText),
      urlencode($seamlessSignature),
      Config::$clientId,
      Config::$oauthScopes,
      Util::generateGuid(),
      $state,
      Config::$oauthTerminalType,
      urlencode(Config::$oauthRedirectUrl),
    );

    return $url;
  }

/**
 * The $authCode is single use only, when $authCode already used for accessToken exchange, and success,
 *  that particular $authCode can not be used again for accessToken exchange: AUTH_CODE_USED
 *
 * The $authCode also have expiry time, it is about 10 minutes, if you try to use it, you will get response:
 *   AUTH_CODE_EXPIRED
 *
 * @param $authCode string, authCode, this authCode is given by DANA via OAuth flow / Account binding flow.
 *
 * @return mixed|null|array ACCESS TOKEN, you need to keep this and save it. This token is required to be used for other operation in api call
 * @return null, if exception happened
 * @return array, ResultInfo if some business process error happened.
 */

  public static function getAccessToken($authCode, $accessToken = '')
  {
    $requestData = [
      'head' => [
        'version'      => '2.0',
        'function'     => 'dana.oauth.auth.applyToken',
        'clientId'     => Config::$clientId,
        'clientSecret' => Config::$clientSecret,
        'reqTime'      => Util::getDateNow(),
        'reqMsgId'     => Util::generateGuid(),
        'accessToken'  => $accessToken ? $accessToken : '',
        'reserve'      => '{}',
      ],
      'body' => [
        'grantType' => 'AUTHORIZATION_CODE',
        'authCode'  => $authCode
      ]
    ];
  
    $response = Util::danaApi("/dana/oauth/auth/applyToken.htm", $requestData);
  
    try {
      $result = json_decode($response, true);
      if (Util::isResponseSuccess($result)) {
        return  $result['response']['body']['accessTokenInfo']['accessToken'];
      }
      return $result['response']['body']['resultInfo'];
    } catch (Exception $e) {}
  
    return null;
  }
}