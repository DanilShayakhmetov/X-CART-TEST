<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\FacebookMarketing\Core;
use XLite\Module\XC\FacebookMarketing\Logic\ProductFeed\Generator;

/**
 * Event listener (common)
 */
 class EventListener extends \XLite\Module\XC\GoogleFeed\Core\EventListener implements \XLite\Base\IDecorator
{
    /**
     * Get listeners
     *
     * @return array
     */
    protected function getListeners()
    {
        return parent::getListeners() + [
            Generator::getEventName() => ['XLite\Module\XC\FacebookMarketing\Core\EventListener\ProductFeedGeneration']
        ];
    }
}