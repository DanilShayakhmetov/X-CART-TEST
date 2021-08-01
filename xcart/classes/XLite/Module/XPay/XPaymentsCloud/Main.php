<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XPay\XPaymentsCloud;

class Main extends \XLite\Module\AModule
{
    /**
     * Service names of payment methods
     */
    const XPAYMENTS_SERVICE_NAME = 'XPaymentsCloud';
    const APPLE_PAY_SERVICE_NAME = 'XPaymentsApplePay';

    /**
     * X-Payments SDK Client
     *
     * @var \XPaymentsCloud\Client
     */
    private static $client = null;

    /**
     * X-Payments Cloud payment method
     *
     * @var \XLite\Model\Payment\Method
     */
    private static $method = null;

    /**
     * X-Payments Apple Pay payment method
     *
     * @var \XLite\Model\Payment\Method
     */
    private static $applePay = null;

    /**
     * Return link to settings form
     *
     * @return string
     */
    public static function getSettingsForm()
    {
        $paymentMethod = static::getPaymentMethod();

        return \XLite\Core\Converter::buildURL(
            'payment_method',
            '',
            ['method_id' => $paymentMethod->getMethodId()]
        );
    }

    /**
     * Get SDK Client
     *
     * @return \XPaymentsCloud\Client
     */
    public static function getClient()
    {
        if (is_null(static::$client)) {

            static::$client = false;

            try {

                require_once LC_DIR_MODULES . 'XPay' . LC_DS . 'XPaymentsCloud' . LC_DS . 'lib' . LC_DS . 'XPaymentsCloud' . LC_DS . 'Client.php';

                $paymentMethod = static::getPaymentMethod();

                if ($paymentMethod) {

                    static::$client = new \XPaymentsCloud\Client(
                        $paymentMethod->getSetting('account'),
                        $paymentMethod->getSetting('api_key'),
                        $paymentMethod->getSetting('secret_key')
                    );
                }

            } catch (\Exception $exception) {

                \XLite\Logger::getInstance()->logCustom('XPaymentsCloud', $exception->getMessage());
            }
        }

        return static::$client;
    }

    /**
     * Get X-Payments Cloud payment method
     *
     * @return \XLite\Model\Payment\Method
     */
    public static function getPaymentMethod()
    {
        if (is_null(static::$method)) {
            static::$method = \XLite\Core\Database::getRepo('XLite\Model\Payment\Method')
                ->findOneBy(['service_name' => self::XPAYMENTS_SERVICE_NAME]);
        }
        return static::$method;
    }

    /**
     * Get X-Payments Apple Pay payment method
     *
     * @return \XLite\Model\Payment\Method
     */
    public static function getApplePayMethod()
    {
        if (is_null(static::$applePay)) {
            static::$applePay = \XLite\Core\Database::getRepo('XLite\Model\Payment\Method')
                ->findOneBy(['service_name' => self::APPLE_PAY_SERVICE_NAME]);
        }

        if (!static::$applePay) {
            static::$applePay = Core\ApplePay::addApplePayMethod();
        }

        return static::$applePay;
    }

    /**
     * Get domain of the storefront
     *
     * @return string
     */
    public static function getStorefrontDomain()
    {
        $host = \XLite::getInstance()->getOptions(array('host_details', 'admin_host'));

        if (empty($host)) {
            $host = \XLite::getInstance()->getOptions(array('host_details', 'https_host'));
        }

        return $host;
    }

    /**
     * Get Account of X-Payments Cloud instance
     *
     * @return string
     */
    protected static function getAccount()
    {
        $host = explode('.', static::getStorefrontDomain());

        return $host[0];
    }

    /**
     * Get shop URL to tie up to X-Payments cloud instance
     *
     * @param int $methodId
     *
     * @return string
     */
    protected static function getShopUrl($methodId)
    {
        return \XLite::getInstance()->getShopURL(
            \XLite::getAdminScript(),
            true,
            array(
                'target' => 'payment_method',
                'method_id' => $methodId,
            )
        );
    }

    /**
     * Register X-Cart Cloud domain (and path if any)
     *
     * @return void
     */
    public static function registerCloudShopUrl()
    {
        $account = static::getAccount();

        if (empty($account)) {
            return static::log('Unable to register shop URL for account');
        }

        $method = static::getPaymentMethod();
        $shop = static::getShopUrl($method->getMethodId());

        $connectUrl = sprintf('https://connect.xpayments.com?target=manual&ref=%s', urlencode($shop));

        $logMessage = sprintf('Register shop URL: %s for %s.xpayments.com', $shop, $account) . PHP_EOL;

        // Execute GET request to obtain form_id for current shop
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $connectUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $body = curl_exec($ch);

        if (curl_errno($ch)) {
            $logMessage .= sprintf('Curl error #%s: %s', curl_errno($ch), curl_error($ch)) . PHP_EOL;
        }

        curl_close($ch);

        if (preg_match('/<input type="hidden" name="form_id" value="(.*)".*>/isU', $body, $m)) {

            $post = array(
                'account' => $account,
                'form_id' => $m[1],
            );

            // Execute POST request to tie up shop domain to X-Payments Cloud account
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $connectUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
            curl_exec($ch);
            curl_close($ch);

            $method->setSetting('account', $account);
            \XLite\Core\Database::getRepo('\XLite\Model\Payment\Method')->update($method);

            $logMessage .= sprintf('Registered account with formID: %s', $m[1]);
        }

        static::log($logMessage);
    }

    /**
     * Add Apple Pay virtual method when X-Payments cloud is added
     *
     * @param \XLite\Model\Payment\Method $method
     *
     * @return void
     */
    public static function onAddPaymentMethod($method)
    {
        if (
            !self::getApplePayMethod()->getAdded()
            || !self::getApplePayMethod()->getEnabled()
        ) {
            self::getApplePayMethod()->setAdded(true);
            self::getApplePayMethod()->setEnabled(true);
        }
    }

    /**
     * Remove Apple Pay virtual method when X-Payments cloud is added
     *
     * @param \XLite\Model\Payment\Method $method
     *
     * @return void
     */
    public static function onRemovePaymentMethod($method)
    {
        self::getApplePayMethod()->setAdded(false);
    }

    /**
     * Logs error in X-PaymentsCloud log file
     *
     * @param string $message Log message
     */
    public static function log($message)
    {
        \XLite\Logger::getInstance()->logCustom('XPaymentsCloud', $message);
    }

    /**
     * @return bool
     */
    public static function isXpaymentsSubscriptionsConfiguredAndActive()
    {
        $paymentMethod = static::getPaymentMethod();

        return $paymentMethod
            && $paymentMethod->isEnabled()
            && static::isSubscriptionsEnabled();
    }

    /**
     * Is subscriptions enabled
     *
     * @return bool
     */
    public static function isSubscriptionsEnabled()
    {
        $isSubscriptionsEnabled = false;
        $client = static::getClient();
        try {
            $response = $client->doGetSubscriptionsSettings();
            $isSubscriptionsEnabled = (bool)$response->isSubscriptionsEnabled;
        } catch (\XPaymentsCloud\ApiException $e) {
            static::log($e->getMessage());
        }

        return $isSubscriptionsEnabled;
    }

    /**
     * Should new subscriptions be created using cloud or not
     *
     * @return bool
     */
    public static function isUseXpaymentsCloudForSubscriptions()
    {
        return (bool)\XLite\Core\Config::getInstance()->XPay->XPaymentsCloud->use_xp_cloud_for_subscriptions;
    }

}
