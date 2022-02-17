<?php

namespace DANA;

class DANAConfigTest extends \PHPUnit\Framework\TestCase
{
    public function testGetWebUrl()
    {
        Config::$isProduction = false;
        $this->assertEquals(Config::SANDBOX_WEB_URL, Config::getWebUrl());

        Config::$isProduction = true;
        $this->assertEquals(Config::PRODUCTION_WEB_URL, Config::getWebUrl());
    }

    public function testGetApiUrl()
    {
        Config::$isProduction = false;
        $this->assertEquals(Config::SANDBOX_API_URL, Config::getApiUrl());

        Config::$isProduction = true;
        $this->assertEquals(Config::PRODUCTION_API_URL, Config::getApiUrl());
    }
}