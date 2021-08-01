<?php

namespace XLite\Module\Kliken\GoogleAds\Logic;

use XLite\Core\Config;
use XLite\Core\Database;

class Helper
{
    const BASE_KLIKEN_URL = 'https://x-cart.kliken.com';
    const PAGE_SLUG       = 'kga_settings';

    /**
     * Wrapper function to X-Cart logging that will only log if in Developer mode
     *
     * @param string  $message
     * @param boolean $withStackTrace
     *
     * @return void
     */
    public static function log($message, $withStackTrace = false, $ignoreDevMode = false)
    {
        if ($ignoreDevMode || LC_DEVELOPER_MODE) {
            \XLite\Logger::logCustom('kliken-googleads', $message, $withStackTrace);
        }
    }

    public static function getModuleId($name, $author, $enabled = true)
    {
        $module = Database::getRepo('\XLite\Model\Module')->findOneBy([
            'name'      => $name,
            'author'    => $author,
            'installed' => 1,
            'enabled'   => $enabled === true ? 1 : 0,
        ]);

        if ($module) {
            return $module->getModuleID();
        }
    }

    /**
     * Determines if a module is enabled
     *
     * @return boolean
     */
    public static function isModuleEnabled($name)
    {
        return Database::getRepo('XLite\Model\Module')->isModuleEnabled($name);
    }

    public static function hasAccountInfo()
    {
		Config::updateInstance();

        return !empty(Config::getInstance()->Kliken->GoogleAds->account_id)
            && !empty(Config::getInstance()->Kliken->GoogleAds->app_token);
    }

    public static function buildCreateAccountLink()
    {
        // Get user info
        $user = \XLite\Core\Auth::getInstance()->getProfile();

        // Generate a session token for the bounce back data
        $token = self::generateKey();
        \XLite\Core\Session::getInstance()->kliken_signup_token = $token;

        return sprintf(
            self::BASE_KLIKEN_URL . '/auth/xcart/?u=%s&n=%s&e=%s&t=%s&return=%s',
            rawurlencode(\XLite::getInstance()->getShopURL()),
            rawurlencode($user->getName()),
            rawurlencode($user->getEmail()),
            $token,
            rawurlencode(\XLite\Core\Converter::buildFullURL(self::PAGE_SLUG))
        );
    }

    /**
     * Post back  REST API module's API keys to Kliken so we can make API requests in the future
     *
     * @param boolean $reloadConfig Whether to reload config values or not.
     * @return boolean
     */
    public static function postBackApiKeys($reloadConfig = false)
    {
        if ($reloadConfig) {
            Config::updateInstance();
        }

        // Check if RESTAPI module has the keys saved or not
        $restKeyWrite = Config::getInstance()->XC->RESTAPI->key;
        $restKeyRead = Config::getInstance()->XC->RESTAPI->key_read;

        if (empty($restKeyWrite)) {
            $restKeyWrite = self::generateKey();

            self::log('REST API Write Key generated! ' . $restKeyWrite);

            // Save the key to database
            Database::getRepo('XLite\Model\Config')->createOption([
                'category' => 'XC\\RESTAPI',
                'name'     => 'key',
                'value'    => $restKeyWrite,
            ]);
        }

        if (empty($restKeyRead)) {
            $restKeyRead = self::generateKey();

            self::log('REST API Read Key generated! ' . $restKeyRead);

            // Save the key to database
            Database::getRepo('XLite\Model\Config')->createOption([
                'category' => 'XC\\RESTAPI',
                'name'     => 'key_read',
                'value'    => $restKeyRead,
            ]);
        }

        // Send back to our Webhook the store URL, and the REST API keys. ONLY if we have account id and app token.
        $accountId = Config::getInstance()->Kliken->GoogleAds->account_id;
        $appToken = Config::getInstance()->Kliken->GoogleAds->app_token;

        if (!empty($accountId) && !empty($appToken)) {
            $hookUrl = 'https://app.mysite-analytics.com/WebHooks/XCartAuth/';

            $request = new \XLite\Core\HTTP\Request($hookUrl);
            $request->verb = 'POST';
            $request->requestTimeout = 10;
            $request->body = json_encode([
                'account_id'     => $accountId,
                'app_token'      => $appToken,
                'store_url'      => \XLite::getInstance()->getShopURL(),
                'rest_key_write' => $restKeyWrite,
                'rest_key_read'  => $restKeyRead,
            ]);
            $request->setHeader('Content-Type', 'application/json');

            self::log('POSTing to WW at: ' . $hookUrl . '. Data: ' . $request->body);

            $response = $request->sendRequest();

            if ($response === null || $response->code !== 200) {
                \XLite\Core\TopMessage::addWarning(
                    \XLite\Core\Translation::getInstance()->translate('Module Kliken\GoogleAds was unable to access your store\'s catalog . Please make sure the correct Account Id and Application Token are saved, or contact Kliken for support.')
                );

                return false;
            }

            return true;
        }
    }

    /**
     * Securely generates a random string to be used as API key
     *
     * @param integer $length
     * @param string $keyspace
     *
     * @return string
     */
    public static function generateKey($length = 32, $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')
    {
        $pieces = [];
        $max = mb_strlen($keyspace, '8bit') - 1;

        for ($i = 0; $i < $length; ++$i) {
            $pieces[] = $keyspace[random_int(0, $max)];
        }

        return implode('', $pieces);
    }
}
