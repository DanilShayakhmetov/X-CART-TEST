<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\GoogleFeed\View\Admin;

use XLite\Module\XC\GoogleFeed\Logic\Feed\Generator;
use XLite\Module\XC\GoogleFeed\Main;

/**
 * Sitemap page widget
 */
class GoogleFeed extends \XLite\View\Dialog
{
    /**
     * Return list of targets allowed for this widget
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $result = parent::getAllowedTargets();
        $result[] = 'google_feed';

        return $result;
    }

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = $this->getDir() . '/style.css';

        return $list;
    }

    /**
     * Return templates directory name
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/XC/GoogleFeed/admin';
    }

    /**
     * @return bool
     */
    protected function isFeedGenerated()
    {
        return Generator::getInstance()
            ? Generator::getInstance()->isGenerated()
            : false;
    }

    /**
     * Get google feed URL
     * 
     * @return string
     */
    protected function getFeedURL()
    {
        return Main::getGoogleFeedUrl();
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function getRenewalFrequency()
    {
        return \XLite\Module\XC\GoogleFeed\Core\Task\FeedUpdater::getRenewalPeriod();
    }
}
