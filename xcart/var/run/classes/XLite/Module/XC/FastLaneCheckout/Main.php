<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\FastLaneCheckout;

abstract class Main extends \XLite\Module\AModule
{
    /**
     * Returns module skin dir
     *
     * @return boolean
     */
    public static function getSkinDir()
    {
        return 'modules/XC/FastLaneCheckout/';
    }

    /**
     * Checks if fastlane checkout mode is enabled
     *
     * @return boolean
     */
    public static function isFastlaneEnabled()
    {
        return 'fast-lane' === \XLite\Core\Config::getInstance()->General->checkout_type;
    }

    /**
     * @return array
     */
    protected static function moveClassesInLists()
    {
        $classes = [];

        $classes['XLite\View\AllInOneSolutions'] = [
            static::TO_ADD => [
                ['checkout_fastlane.header.top', 100, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
            ],
        ];

        return $classes;
    }

    /**
     * @return array
     */
    protected static function moveTemplatesInLists()
    {
        $templates = [
            'layout/header/header.bar.checkout.logos.twig' => [
                static::TO_DELETE => [],
                static::TO_ADD    => [
                    ['checkout_fastlane.header.top', 100, \XLite\Model\ViewList::INTERFACE_CUSTOMER],
                ],
            ],
        ];

        return $templates;
    }
}
