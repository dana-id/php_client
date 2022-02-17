<?php

namespace DANA;

class DANAAuthTest extends \PHPUnit\Framework\TestCase
{
    public function testQueryUserProfile()
    {
      Config::$isProduction = false;
      $queryUserProfile = Auth::getUserProfile('1111');
      $this->assertEquals(
        [
          'resultCode' => 'OAUTH_FAILED',
          'resultCodeId' => '00000016',
          'resultStatus' => 'F',
          'resultMsg' => 'OAUTH_FAILED'
        ], 
        $queryUserProfile
      );
    }

    public function testLoginUrl()
    {
      Config::$isProduction = false;
      $state = Util::generateGuid();
      $url = Auth::generateLoginUrl($state);
      $this->assertNotEmpty($url); 
    }

    public function testSeamlessLoginUrl()
    {
      Config::$isProduction = false;
      $state = Util::generateGuid();
      $url = Auth::generateLoginUrl($state);
      $clientId = Config::$clientId;
      $this->assertStringContainsString($clientId, $url);
      $this->assertStringContainsString(Config::$oauthScopes, $url);
      $this->assertStringContainsString($state, $url);
      $this->assertStringContainsString(Config::$oauthTerminalType, $url);
      $this->assertStringContainsString(urlencode(Config::$oauthRedirectUrl), $url);
    }

    public function testGetAccessToken()
    {
      $authCode = 'lrz2LLkitDFVhGdY8hf6WhvsSJ7q3aaO4lQV0500';
      $accessToken = Auth::getAccessToken($authCode);

      $this->assertEquals([
        'resultStatus' => 'F',
        'resultCodeId' => '12014155',
        'resultMsg' => 'Not found Client',
        'resultCode' => 'UNKNOWN_CLIENT'
      ], $accessToken);
    }
}