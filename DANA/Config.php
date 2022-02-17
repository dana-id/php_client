<?php

namespace DANA;

class Config
{
    /**
     * True for production
     * false for sandbox mode
     * 
     * @static
     */
    public static $isProduction = false;

    /**
     * True for mockApi mode
     * false for switch off mockApi
     * 
     * @static
     */
    public static $isMockApi = false;

    /**
     * for mockScene mode
     * 
     * @static
     */
    public static $isMockScene = '';

    const DANA_DATE_FORMAT = "Y-m-d\TH:i:sP";
    // DANA environment setup
    // API Server domain
    const SANDBOX_API_URL = "https://api.sandbox.dana.id";
    const PRODUCTION_API_URL = "https://api.saas.dana.id";

    /**
     * have data for for custom apiUrl
     * 
     * @static
     */
    public static $apiUrl = "";

    // IPG Server domain
    const SANDBOX_WEB_URL = "https://m.sandbox.dana.id";
    const PRODUCTION_WEB_URL = "https://m.dana.id";

    /**
     * have data for for custom webUrl
     * 
     * @static
     */
    public static $webUrl = "";

    /**
     * have data for for custom publicKey
     * example $publicKey = "-----BEGIN PUBLIC KEY-----\nMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAnaKVGRbin4Wh4KN35OPh\nytJBjYTz7QZKSZjmHfiHxFmulfT87rta+IvGJ0rCBgg+1EtKk1hX8G5gPGJs1htJ\n5jHa3/jCk9l+luzjnuT9UVlwJahvzmFw+IoDoM7hIPjsLtnIe04SgYo0tZBpEmkQ\nvUGhmHPqYnUGSSMIpDLJDvbyr8gtwluja1SbRphgDCoYVXq+uUJ5HzPS049aaxTS\nnfXh/qXuDoB9EzCrgppLDS2ubmk21+dr7WaO/3RFjnwx5ouv6w+iC1XOJKar3CTk\nX6JV1OSST1C9sbPGzMHZ8AGB51BM0mok7davD/5irUk+f0C25OgzkwtxAt80dkDo\n/QIDAQAB\n-----END PUBLIC KEY-----"
     * 
     * @static
     */
    public static $publicKey = "-----BEGIN PUBLIC KEY-----\nMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAnaKVGRbin4Wh4KN35OPh\nytJBjYTz7QZKSZjmHfiHxFmulfT87rta+IvGJ0rCBgg+1EtKk1hX8G5gPGJs1htJ\n5jHa3/jCk9l+luzjnuT9UVlwJahvzmFw+IoDoM7hIPjsLtnIe04SgYo0tZBpEmkQ\nvUGhmHPqYnUGSSMIpDLJDvbyr8gtwluja1SbRphgDCoYVXq+uUJ5HzPS049aaxTS\nnfXh/qXuDoB9EzCrgppLDS2ubmk21+dr7WaO/3RFjnwx5ouv6w+iC1XOJKar3CTk\nX6JV1OSST1C9sbPGzMHZ8AGB51BM0mok7davD/5irUk+f0C25OgzkwtxAt80dkDo\n/QIDAQAB\n-----END PUBLIC KEY-----";
    
    /**
     * Get webUrl
     * 
     * @return string DANA WEB URL, depends on $isProduction
     */
    public static function getWebUrl()
    {
        if (!self::$isProduction && !empty(self::$webUrl)) {
            return self::$webUrl;
        }
        return self::$isProduction ?
        self::PRODUCTION_WEB_URL : self::SANDBOX_WEB_URL;
    }

    /**
     * Get apiUrl
     * 
     * @return string DANA API URL, depends on $isProduction
     */
    public static function getApiUrl()
    {
        if (!self::$isProduction && !empty(self::$apiUrl)) {
            return self::$apiUrl;
        }
        return self::$isProduction ?
        self::PRODUCTION_API_URL : self::SANDBOX_API_URL;
    }

    /**
     * for clientId value
     * example = 2018122812174155520063
     * 
     * @static
     */
    public static $clientId = '2018122812174155520063';

    /**
     * for clientSecret value
     * example = 3f5798274c9b427e9e0aa2c5db0a6454
     * 
     * @static
     */
    public static $clientSecret = '3f5798274c9b427e9e0aa2c5db0a6454';

    /**
     * for merchantId value
     * example = 216620000000140414598
     * 
     * @static
     */
    public static $merchantId = '216620000000140414598';

    /**
     * for privateKey value
     * Put your private key here, the public key (counter part/pair) will be configured on DANA backend.
     * example for $privateKey = "-----BEGIN PRIVATE KEY-----\nMIIEvAIBADANBgkqhkiG9w0BAQEFAASCBKYwggSiAgEAAoIBAQDF6yl3knDrtnMZ\nhFB44HCiGBRx2aQIqQV2WyYjj3NgfNUmvly6rhZe6DpURLbx9yrwi4R+3CuXXa6v\nAxAMIiRVmhnrK4lbGIyv9gMMTQ8ftRBYGgppMIWVz0RyQvQxgbj3hEyD+5550sCM\n1CfrUHTABdVCf/Q4Yl9cDrHckJVHnkSgGv9tjIS9XdnxeBRdwUAjaf0M1MCwSKvi\nAXnTMvbuovMLD1ZzIwajnUU/9vOUoodALnV1G7FlFVNwt0ZjKVRG4EpJcFDXlVBF\nTVqsNOfT5yDUllMZXuJX9UwEFlXsSoah6FoniQEaiWuSa6d6GiC4aanngU5D0mkZ\nHmw7glhPAgMBAAECggEAf08hQVTlZfnaV9OQn/BfAyVSIVnJ9fgjF1rSUZV4tdwW\nj/FrXHmW0j23J3V76HEBIfHcrG2bbKZKMzFZJTThAl85xNggZoSIGre4hjUbQV4K\nwWEeyUL46sCEWqtOwHmdLTngrhUwf1Rdnzjo5KjAMLSP1VLRGWPNyz14k1Q9ce7t\nUDj9sJlMU6gfkIpbdWTjf4FOngPoGtwEu++bKMf1ySwRynZViLjBjPwK8WXfz19N\nGBudAROUPSHQn5H1Mqr0fuBCWZlRFpkn5nVVju/omEIC4W7UdS5Vl3d3MX44TBbh\nbRkWPf50rsX59fAqpQoOvVMgE/lQltTHr290wTKaAQKBgQD48iIr+HP0m4q2HLnd\nQgv1jJQ8e8zrwiHoPAq13A7oOXSFcErAxz3p6E928rUeQ5qbN5mZHuG3FpUJWfwL\nU59NykuvrDcozwtNANigZJm3l+aqjNUl/YJOpiQHdiOrs7BtYtjjAnKp7LM62s5O\nJM76JYk6sVoZJazgSVj722EYQQKBgQDLht/LgTLpr64NgPoXiCPPC03bYzosbtfM\nLLPZmyg+gUj79QgQd4KfslrylVjagdYfiJCm0fW8s3cYy2PgZ2XWVM/dq2k8V0ap\n53aruljBkJcw9TqFbgtkgtUvzevN13GrbZYQeTniy3FtirArtTagWM9+yZCspsxG\nZkHthHLMjwKBgH3IEZ333da0lERphTuW+GXrzqY0wxhHsUwesiaq9lk9VnNphkub\nH9xEhYoLHZoZ/E76q7/jC5avcUQVVDUA3le2o8EyGXikDAivKcT4r3ZE6MY5fCTC\nzwkgBspCVcmWc8kBDaa9gOP8dZ6CGYUAMhfPyWN/Yo+cKpn0VWKDuK5BAoGAaMfL\nN64QVBbJ+NHJ74t7RACB2MzBClcWalspTIKAuY090dlYiYG9khH0mKci03u8jQd1\n0SyO4tNSIUW06bbRywJm8axpxVL5Ykdz5P1O7HhQHjhiJId+/gQNVUsidxrPvn3w\noBKJJqWug8K+6AGnWe3fBVsoTHqi+Ct1DZ7+qykCgYBSnx/dq4fJVgP+QvgsGLHc\nKd3FQS1u5jRAjYKqnMTIiV9R0wNWSpvQPAaKzCJ/LtmJejNHbaXRfyCv7KHKz3rJ\nwbLmXAZGiXAC/SkcnHEKAB+AQ6j3Id+9fC8sn/xwlbodkOoxbdd7Ac88B8psLx3t\nDOTolJxRmnEXuv32sWB8KA==\n-----END PRIVATE KEY-----"
     * 
     * @static
     */
    public static $privateKey = "-----BEGIN PRIVATE KEY-----\nMIIEvAIBADANBgkqhkiG9w0BAQEFAASCBKYwggSiAgEAAoIBAQDF6yl3knDrtnMZ\nhFB44HCiGBRx2aQIqQV2WyYjj3NgfNUmvly6rhZe6DpURLbx9yrwi4R+3CuXXa6v\nAxAMIiRVmhnrK4lbGIyv9gMMTQ8ftRBYGgppMIWVz0RyQvQxgbj3hEyD+5550sCM\n1CfrUHTABdVCf/Q4Yl9cDrHckJVHnkSgGv9tjIS9XdnxeBRdwUAjaf0M1MCwSKvi\nAXnTMvbuovMLD1ZzIwajnUU/9vOUoodALnV1G7FlFVNwt0ZjKVRG4EpJcFDXlVBF\nTVqsNOfT5yDUllMZXuJX9UwEFlXsSoah6FoniQEaiWuSa6d6GiC4aanngU5D0mkZ\nHmw7glhPAgMBAAECggEAf08hQVTlZfnaV9OQn/BfAyVSIVnJ9fgjF1rSUZV4tdwW\nj/FrXHmW0j23J3V76HEBIfHcrG2bbKZKMzFZJTThAl85xNggZoSIGre4hjUbQV4K\nwWEeyUL46sCEWqtOwHmdLTngrhUwf1Rdnzjo5KjAMLSP1VLRGWPNyz14k1Q9ce7t\nUDj9sJlMU6gfkIpbdWTjf4FOngPoGtwEu++bKMf1ySwRynZViLjBjPwK8WXfz19N\nGBudAROUPSHQn5H1Mqr0fuBCWZlRFpkn5nVVju/omEIC4W7UdS5Vl3d3MX44TBbh\nbRkWPf50rsX59fAqpQoOvVMgE/lQltTHr290wTKaAQKBgQD48iIr+HP0m4q2HLnd\nQgv1jJQ8e8zrwiHoPAq13A7oOXSFcErAxz3p6E928rUeQ5qbN5mZHuG3FpUJWfwL\nU59NykuvrDcozwtNANigZJm3l+aqjNUl/YJOpiQHdiOrs7BtYtjjAnKp7LM62s5O\nJM76JYk6sVoZJazgSVj722EYQQKBgQDLht/LgTLpr64NgPoXiCPPC03bYzosbtfM\nLLPZmyg+gUj79QgQd4KfslrylVjagdYfiJCm0fW8s3cYy2PgZ2XWVM/dq2k8V0ap\n53aruljBkJcw9TqFbgtkgtUvzevN13GrbZYQeTniy3FtirArtTagWM9+yZCspsxG\nZkHthHLMjwKBgH3IEZ333da0lERphTuW+GXrzqY0wxhHsUwesiaq9lk9VnNphkub\nH9xEhYoLHZoZ/E76q7/jC5avcUQVVDUA3le2o8EyGXikDAivKcT4r3ZE6MY5fCTC\nzwkgBspCVcmWc8kBDaa9gOP8dZ6CGYUAMhfPyWN/Yo+cKpn0VWKDuK5BAoGAaMfL\nN64QVBbJ+NHJ74t7RACB2MzBClcWalspTIKAuY090dlYiYG9khH0mKci03u8jQd1\n0SyO4tNSIUW06bbRywJm8axpxVL5Ykdz5P1O7HhQHjhiJId+/gQNVUsidxrPvn3w\noBKJJqWug8K+6AGnWe3fBVsoTHqi+Ct1DZ7+qykCgYBSnx/dq4fJVgP+QvgsGLHc\nKd3FQS1u5jRAjYKqnMTIiV9R0wNWSpvQPAaKzCJ/LtmJejNHbaXRfyCv7KHKz3rJ\nwbLmXAZGiXAC/SkcnHEKAB+AQ6j3Id+9fC8sn/xwlbodkOoxbdd7Ac88B8psLx3t\nDOTolJxRmnEXuv32sWB8KA==\n-----END PRIVATE KEY-----";
    
    /**
     * for oauthRedirectUrl value
     * Put your redirect url for OAuth flow/account binding, to redirect the authCode
     * example = https://api.merchant.com/oauth-callback
     * 
     * @static
     */
    public static $oauthRedirectUrl = 'https://api.merchant.com/oauth-callback';

    /**
     * for merchantMcc value
     * your MCC, will be used in createOrder api
     * example = '123'
     * 
     * @static
     */
    public static $merchantMcc = '123';

    /**
     * for acquirementPayReturnUrl value
     * This url define the url that will be targeted when user finish doing payment in Cashier Page
     * If you keep this value empty, DANA will fetch the value from merchant onboarding process
     * example = https://web.merchant.com/success
     * 
     * @static
     */
    public static $acquirementPayReturnUrl = 'https://web.merchant.com/success';

    /**
     * for acquirementNotificationUrl value
     * For each transaction finished in DANA, DANA backend will send notification to this URL
     * If you keep this value empty, DANA will fetch the value from merchant onboarding process
     * example = https://api.merchant.com/success
     * 
     * @static
     */
    public static $acquirementNotificationUrl = 'https://api.merchant.com/success';
    
    /**
     * for checkoutStrategy value
     * use "GUEST_CHECKOUT" to checkout, without account binding
     * use "NORMAL_CHECKOUT" with account binding
     * "NORMAL_CHECKOUT" This checkout requires 'ott' in the url, the ott will be acquired from accessToken,
     * "NORMAL_CHECKOUT" and accessToken will be acquired from OAuth flow/Account binding
     * 
     * @static
     */
    public static $checkoutStrategy = 'NORMAL_CHECKOUT';
    
    /**
     * for oauthTerminalType value
     * Account binding
     * 
     * @static
     */
    public static $oauthTerminalType = 'WEB';

    /**
     * for oauthScopes value
     * Account binding
     * 
     * @static
     */
    public static $oauthScopes = 'CASHIER,QUERY_BALANCE,DEFAULT_BASIC_PROFILE,MINI_DANA';
    
    /**
     * for refundActor value
     * Api configuration
     * 
     * @static
     */
    public static $refundActor = 'SYSTEM';

    /**
     * for refundDestination value
     * Api configuration
     * 
     * @static
     */
    public static $refundDestination = 'TO_BALANCE';

    // PHP setting
    // You can omit this if you already set it in php.ini
    // This config is required so all DateTime operation will refer to correct timezone.
    // ini_set('date.timezone', 'Asia/Jakarta');
}