<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\FacebookMarketing\View;

use Includes\Utils\Module\Manager;
use Includes\Utils\Module\Module;

/**
 * Google feed promo banner
 */
class GoogleFeedBanner extends \XLite\View\AView
{
    use \XLite\Core\Cache\ExecuteCachedTrait;

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/XC/FacebookMarketing/google_feed_banner/body.twig';
    }

    /**
     * @return string
     */
    protected function getGoogleFeedPromoText()
    {
        return static::t('1. Use the addon Google Product Feed for advanced flexibility generating a data feed for Facebook based on the product attributes and variants from your store catalog 2. Generate Product Feed', ['href' => $this->getGoogleFeedModuleLink()]);
    }

    /**
     * @return bool
     */
    protected function isGoogleFeedEnabled()
    {
        return Manager::getRegistry()->isModuleEnabled('XC', 'GoogleFeed');
    }

    /**
     * @return string
     */
    protected function getGoogleFeedModuleLink()
    {
        return $this->isGoogleFeedEnabled()
            ? $this->buildURL('google_shopping_groups')
            : Manager::getRegistry()->getModuleServiceURL('XC', 'GoogleFeed');
    }
}
