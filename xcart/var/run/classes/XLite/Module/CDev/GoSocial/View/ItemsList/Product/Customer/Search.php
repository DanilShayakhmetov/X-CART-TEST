<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\GoSocial\View\ItemsList\Product\Customer;

/**
 * Search
 *
 */
 class Search extends \XLite\Module\CDev\GoogleAnalytics\View\ItemsList\Product\Customer\Search implements \XLite\Base\IDecorator
{
    use \XLite\Module\CDev\GoSocial\Core\OpenGraphTrait;

    /**
     * Register Meta tags
     *
     * @return array
     */
    public function getMetaTags()
    {
        $list = parent::getMetaTags();
        $list[] = $this->getOpenGraphMetaTags(false);

        return $list;
    }

    /**
     * @inheritdoc
     */
    protected function getOpenGraphTitle()
    {
        return \XLite::getController()->getPageTitle() . ' : ' . self::getParam('substring');
    }

    /**
     * @inheritdoc
     */
    protected function getOpenGraphType()
    {
        return 'product.group';
    }

    /**
     * Returns open graph url
     *
     * @return string
     */
    protected function getOpenGraphURL()
    {
        return \XLite\Core\URLManager::getCurrentURL();
    }

    /**
     * @inheritdoc
     */
    protected function getOpenGraphDescription()
    {
        return strip_tags($this->getListHead()) . ' - ' . static::t('default-meta-description');
    }

    /**
     * @inheritdoc
     */
    protected function preprocessOpenGraphMetaTags($tags)
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    protected function isUseOpenGraphImage()
    {
        return false;
    }

    /**
     * Return OgMeta
     *
     * @return string
     */
    public function getOgMeta()
    {
        return false;
    }
}
