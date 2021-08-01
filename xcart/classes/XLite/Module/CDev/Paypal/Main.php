<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal;

use Includes\Utils\Module\Manager;

abstract class Main extends \XLite\Module\AModule
{
    /**
     * Paypal methods service names
     */
    const PP_METHOD_PPA  = 'PaypalAdvanced';
    const PP_METHOD_PFL  = 'PayflowLink';
    const PP_METHOD_PFTR = 'PayflowTransparentRedirect';
    const PP_METHOD_EC   = 'ExpressCheckout';
    const PP_METHOD_PPS  = 'PaypalWPS';
    const PP_METHOD_PC   = 'PaypalCredit';
    const PP_METHOD_PAD  = 'PaypalAdaptive';
    const PP_METHOD_PFM  = 'PaypalForMarketplaces';
    const PP_METHOD_PCP  = 'PaypalCommercePlatform';

    /**
     * RESTAPI instance
     *
     * @var \XLite\Module\CDev\Paypal\Core\RESTAPI
     */
    protected static $RESTAPI;

    /**
     * Payment methods
     *
     * @var \XLite\Model\Payment\Method[]
     */
    protected static $paymentMethod = [];

    /**
     * Defines the link for the payment settings form
     *
     * @return string
     */
    public static function getPaymentSettingsForm()
    {
        return Manager::getRegistry()->getModuleSettingsUrl('CDev', 'Paypal');
    }

    /**
     * Add record to the module log file
     *
     * @param string $message Text message OPTIONAL
     * @param mixed  $data    Data (can be any type) OPTIONAL
     *
     * @return void
     */
    public static function addLog($message = null, $data = null)
    {
        if ($message && $data) {
            $msg = [
                'message' => $message,
                'data'    => $data,
            ];

        } else {
            $msg = ($message ?: ($data ?: null));
        }

        \XLite\Logger::logCustom(
            'Paypal',
            $msg
        );
    }

    /**
     * Returns payment method
     *
     * @param string  $serviceName Service name
     * @param boolean $enabled     Enabled status OPTIONAL
     *
     * @return \XLite\Model\Payment\Method
     */
    public static function getPaymentMethod($serviceName, $enabled = null)
    {
        if (!isset(static::$paymentMethod[$serviceName])) {
            static::$paymentMethod[$serviceName] = \XLite\Core\Database::getRepo('XLite\Model\Payment\Method')
                ->findOneBy(['service_name' => $serviceName]);
            if (!static::$paymentMethod[$serviceName]) {
                static::$paymentMethod[$serviceName] = false;
            }
        }

        return static::$paymentMethod[$serviceName]
        && (
            is_null($enabled)
            || static::$paymentMethod[$serviceName]->getEnabled() === (bool) $enabled
        )
            ? static::$paymentMethod[$serviceName]
            : null;
    }

    /**
     * Returns true if ExpressCheckout payment is enabled
     *
     * @param \XLite\Model\Cart $order Cart object OPTIONAL
     *
     * @return boolean
     */
    public static function isExpressCheckoutEnabled($order = null)
    {
        static $result;

        $index = (null !== $order) ? 1 : 0;

        if (!isset($result[$index])) {
            $paymentMethod  = static::getPaymentMethod(static::PP_METHOD_EC, true);
            $result[$index] = $paymentMethod && $paymentMethod->isEnabled();

            if ($order && $result[$index]) {
                $result[$index] = $paymentMethod->getProcessor()->isApplicable($order, $paymentMethod);
            }
        }

        return $result[$index];
    }

    /**
     * Returns true if ForMarketplaces payment is enabled
     *
     * @param \XLite\Model\Cart $order Cart object OPTIONAL
     *
     * @return boolean
     */
    public static function isPaypalForMarketplacesEnabled($order = null)
    {
        static $result;

        $index = (null !== $order) ? 1 : 0;

        if (!isset($result[$index])) {
            $paymentMethod  = static::getPaymentMethod(static::PP_METHOD_PFM, true);
            $result[$index] = $paymentMethod && $paymentMethod->isEnabled();

            if ($order && $result[$index]) {
                $result[$index] = $paymentMethod->getProcessor()->isApplicable($order, $paymentMethod);
            }
        }

        return $result[$index];
    }

    /**
     * Returns true if CommercePlatform payment is enabled
     *
     * @param \XLite\Model\Cart $order Cart object OPTIONAL
     *
     * @return boolean
     */
    public static function isPaypalCommercePlatformEnabled($order = null)
    {
        static $result;

        $index = (null !== $order) ? 1 : 0;

        if (!isset($result[$index])) {
            $paymentMethod  = static::getPaymentMethod(static::PP_METHOD_PCP, true);
            $result[$index] = $paymentMethod && $paymentMethod->isEnabled();

            if ($order && $result[$index]) {
                $result[$index] = $paymentMethod->getProcessor()->isApplicable($order, $paymentMethod);
            }
        }

        return $result[$index];
    }

    /**
     * Returns true if Advanced payment is enabled
     *
     * @param \XLite\Model\Cart $order Cart object OPTIONAL
     *
     * @return boolean
     */
    public static function isPaypalAdvancedEnabled($order = null)
    {
        static $result;

        $index = (null !== $order) ? 1 : 0;

        if (!isset($result[$index])) {
            $paymentMethod  = static::getPaymentMethod(static::PP_METHOD_PPA, true);
            $result[$index] = $paymentMethod && $paymentMethod->isEnabled();

            if ($order && $result[$index]) {
                $result[$index] = $paymentMethod->getProcessor()->isApplicable($order, $paymentMethod);
            }
        }

        return $result[$index];
    }

    /**
     * Returns BuyNow button availability status
     *
     * @return boolean
     */
    public static function isBuyNowEnabled()
    {
        static $result;

        if (null === $result) {
            $paymentMethod = static::getPaymentMethod(static::PP_METHOD_EC, true) ?? static::getPaymentMethod(static::PP_METHOD_PCP, true);
            if ($paymentMethod) {
                $result = (bool) $paymentMethod->getSetting('buyNowEnabled');
            }
        }

        return $result;
    }

    /**
     * Returns Header badge availability status
     *
     * @return boolean
     */
    public static function isHeadIconEnabled()
    {
        static $result;

        if (null === $result) {
            $paymentMethod = static::getPaymentMethod(static::PP_METHOD_EC, true) ?? static::getPaymentMethod(static::PP_METHOD_PCP, true);
            if ($paymentMethod) {
                $result = (bool) $paymentMethod->getSetting('headIconEnabled');
            }
        }

        return $result;
    }

    /**
     * Returns true if PaypalCredit payment is enabled
     *
     * @param \XLite\Model\Cart $order Cart object OPTIONAL
     *
     * @return boolean
     */
    public static function isPaypalCreditEnabled($order = null)
    {
        static $result;

        $index = (null !== $order ? 1 : 0);

        if (!isset($result[$index])) {
            if (\XLite\Core\Config::getInstance()->Company->location_country === 'US') {
                $paymentMethod  = static::getPaymentMethod(static::PP_METHOD_PC, true);
                $result[$index] = $paymentMethod
                    && $paymentMethod->isEnabled()
                    && $paymentMethod->getSetting('enabled')
                    && static::isExpressCheckoutEnabled($order);
            } else {
                $result[$index] = false;
            }
        }

        return $result[$index];
    }

    /**
     * Returns true if PaypalCredit payment is enabled
     *
     * @param \XLite\Model\Cart $order Cart object OPTIONAL
     *
     * @return boolean
     */
    public static function isPaypalCreditForCommercePlatformEnabled($order = null)
    {
        static $result;

        $index = (null !== $order ? 1 : 0);

        if (!isset($result[$index])) {
            if (
                \XLite\Core\Config::getInstance()->Company->location_country === 'US'
                && static::isPaypalCommercePlatformEnabled($order)
            ) {
                $paymentMethod  = static::getPaymentMethod(static::PP_METHOD_PC);
                $result[$index] = $paymentMethod
                    && $paymentMethod->getSetting('enabled');

            } else {
                $result[$index] = false;
            }
        }

        return $result[$index];
    }

    /**
     * Returns true if PaypalWPS payment is enabled
     *
     * @param \XLite\Model\Cart $order Cart object OPTIONAL
     *
     * @return boolean
     */
    public static function isPaypalWPSEnabled($order = null)
    {
        static $result;

        $index = (null !== $order) ? 1 : 0;

        if (!isset($result[$index])) {
            $paymentMethod  = static::getPaymentMethod(static::PP_METHOD_PPS, true);
            $result[$index] = $paymentMethod && $paymentMethod->isEnabled();
        }

        return $result[$index];
    }

    /**
     * Returns true if PaypalAdaptive payment is enabled
     *
     * @param \XLite\Model\Cart $order Cart object OPTIONAL
     *
     * @return boolean
     */
    public static function isPaypalAdaptiveEnabled($order = null)
    {
        static $result;

        $index = (null !== $order) ? 1 : 0;

        if (!isset($result[$index])) {
            $paymentMethod  = static::getPaymentMethod(static::PP_METHOD_PAD, true);
            $result[$index] = $paymentMethod && $paymentMethod->isEnabled();
        }

        return $result[$index];
    }

    /**
     * Get logo
     *
     * @return string
     */
    public static function getLogo()
    {
        return \XLite\Core\URLManager::getShopURL(
            \XLite\Core\Layout::getInstance()->getLogo(),
            true,
            [],
            \XLite\Core\URLManager::URL_OUTPUT_FULL,
            false
        );
    }

    /**
     * Get logo
     *
     * @return string
     */
    public static function getSignUpLogo()
    {
        $logo = \XLite\Core\Layout::getInstance()->getResourceWebPath(
            'modules/CDev/Paypal/signup_logo.png',
            \XLite\Core\Layout::WEB_PATH_OUTPUT_URL,
            \XLite::ADMIN_INTERFACE
        );

        return \XLite\Core\URLManager::getShopURL(
            $logo,
            true,
            [],
            \XLite\Core\URLManager::URL_OUTPUT_FULL,
            false
        );
    }

    /**
     * Return RESTAPI instance
     *
     * @return \XLite\Module\CDev\Paypal\Core\RESTAPI
     */
    public static function getRESTAPIInstance()
    {
        if (null === static::$RESTAPI) {
            static::$RESTAPI = new \XLite\Module\CDev\Paypal\Core\RESTAPI();
        }

        return static::$RESTAPI;
    }

    /**
     * Returns paypal methods service codes
     *
     * @return array
     */
    public static function getServiceCodes()
    {
        return [
            static::PP_METHOD_PPA,
            static::PP_METHOD_PFL,
            static::PP_METHOD_EC,
            static::PP_METHOD_PPS,
            static::PP_METHOD_PC,
            static::PP_METHOD_PAD,
            static::PP_METHOD_PFM,
            static::PP_METHOD_PCP,
        ];
    }

    /**
     * Method to initialize concrete module instance
     *
     * @return void
     */
    public static function init()
    {
        include_once LC_DIR_MODULES . 'CDev' . LC_DS . 'Paypal' . LC_DS . 'lib' . LC_DS . 'autoload.php';
    }
}
