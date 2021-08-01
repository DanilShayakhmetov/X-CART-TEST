<?php

namespace XLite\Module\Kliken\GoogleAds;

use XLite\Module\Kliken\GoogleAds\Logic\Helper;

abstract class Main extends \XLite\Module\AModule
{
    /**
     * Author name
     *
     * @return string
     */
    public static function getAuthorName()
    {
        return 'Kliken';
    }

    /**
     * Module name
     *
     * @return string
     */
    public static function getModuleName()
    {
        return 'Google Ads by Kliken';
    }

    /**
     * Get module major version
     *
     * @return string
     */
    public static function getMajorVersion()
    {
        return '5.3';
    }

    /**
     * Module minor version
     *
     * @return string
     */
    public static function getMinorVersion()
    {
        return '5';
    }

    /**
     * Get module build number (4th number in the version)
     *
     * @return string
     */
    public static function getBuildVersion()
    {
        return '3';
    }

    /**
     * Module description
     *
     * @return string
     */
    public static function getDescription()
    {
        return 'The automated Google Shopping solution to get your products found on Google, and grow your X-Cart Store!';
    }

    /**
     * Return module dependencies
     *
     * @return array
     */
    public static function getDependencies()
    {
        return [
            'XC\RESTAPI'
        ];
    }

    /**
     * Determines if we need to show settings form link
     *
     * @return boolean
     */
    public static function showSettingsForm()
    {
        return true;
    }

    /**
     * Return link to settings form
     *
     * @return string
     */
    public static function getSettingsForm()
    {
        return \XLite\Core\Converter::buildURL(Helper::PAGE_SLUG);
    }

    public static function callInstallEvent()
    {
        // For backward compatibility, according to X-Cart
        parent::callInstallEvent();

        Helper::log('Running callInstallEvent()...');

        Helper::postBackApiKeys();
    }

    public static function runBuildCacheHandler()
    {
        // For backward compatibility, according to X-Cart
        parent::runBuildCacheHandler();

        Helper::log('Running runBuildCacheHandler()...');

        Helper::postBackApiKeys();
    }

    /**
     * Method to initialize concrete module instance
     *
     * @return void
     */
    public static function init()
    {
        include_once LC_DIR_MODULES . 'Kliken' . LC_DS . 'GoogleAds' . LC_DS . 'lib' . LC_DS . 'autoload.php';
    }
}
