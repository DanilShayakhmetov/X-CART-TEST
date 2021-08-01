<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace XLite\Core;

/**
 * Events subsystem
 */
class MobileDetect extends \XLite\Base\Singleton
{
    /**
     * Device detection
     *
     * @var \Mobile_Detect
     */
    public $detect;

    /**
     * Method to access a singleton
     *
     * @return \Mobile_Detect
     */
    public static function getInstance()
    {
        return parent::getInstance()->detect;
    }

    /**
     * Check if the mobile device is a phone
     *
     * @return boolean
     */
    public static function isMobilePhone()
    {
        $instance = static::getInstance();

        return $instance->isMobile() && !$instance->isTablet();
    }

    /**
     * Constructor
     *
     * @return void
     */
    protected function __construct()
    {
        $this->detect = new \Mobile_Detect;
    }
}