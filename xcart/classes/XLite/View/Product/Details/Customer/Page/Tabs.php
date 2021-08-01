<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Product\Details\Customer\Page;

/**
 * Product tabs
 */
class Tabs extends \XLite\View\Product\Details\Customer\Page\APage
{
    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'product/details/page/tabs.twig';
    }

    /**
     * Cache availability
     *
     * @return boolean
     */
    protected function isCacheAvailable()
    {
        return true;
    }

    /**
     * Get cache parameters
     *
     * @return array
     */
    protected function getCacheParameters()
    {
        $params = parent::getCacheParameters();

        if ($this->getProduct()) {
            $params[] = $this->getProduct()->getProductId();
        }
        $params[] = \XLite\Core\Database::getRepo('XLite\Model\Product\GlobalTab')->getVersion();

        return $params;
    }
}
