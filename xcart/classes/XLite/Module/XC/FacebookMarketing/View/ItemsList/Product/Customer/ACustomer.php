<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\FacebookMarketing\View\ItemsList\Product\Customer;

/**
 * Product list widget
 */
abstract class ACustomer extends \XLite\View\ItemsList\Product\Customer\ACustomer implements \XLite\Base\IDecorator
{
    protected function getPixelContentLists()
    {
        return [
            'main'          => ['\XLite\Module\CDev\FeaturedProducts\View\Customer\FeaturedProducts'],
            'category'      => [
                '\XLite\Module\XC\ProductFilter\View\ItemsList\Product\Customer\Category\CategoryFilter',
                '\XLite\View\ItemsList\Product\Customer\Category\Main',
            ],
            'sale_products' => ['\XLite\Module\CDev\Sale\View\SalePage'],
            'coming_soon'   => ['\XLite\Module\CDev\ProductAdvisor\View\ComingSoonPage'],
            'new_arrivals'  => ['\XLite\Module\CDev\ProductAdvisor\View\NewArrivalsPage'],
            'bestsellers'   => ['\XLite\Module\CDev\Bestsellers\View\BestsellersPage'],
            'search'        => ['\XLite\View\ItemsList\Product\Customer\Search'],
        ];
    }

    protected function getJSData()
    {
        $jsData = parent::getJSData();

        $pixelData = [];
        $target = \XLite::getController()->getTarget();
        $contentLists = $this->getPixelContentLists();
        if (isset($contentLists[$target])) {
            foreach ($contentLists[$target] as $list) {
                if (is_a($this, $list)) {
                    $pixelData['content_ids'] = $this->getFbPixelProductIds();
                    break;
                }
            }
        }

        if ('search' == $target && is_a($this, '\XLite\View\ItemsList\Product\Customer\Search')) {
            $pixelData['search_string'] = $this->getParam(static::PARAM_SUBSTRING) ?: '';
        }

        if ($pixelData) {
            $jsData['fb_pixel_content_data'] = $pixelData;
        }

        return $jsData;
    }

    protected function getFbPixelProductIds()
    {
        $pageData = $this->getPageData();

        $ids = [];
        foreach (array_slice($pageData, 0, 10) as $product) {
            $ids[] = $product->getFacebookPixelProductIdentifier();
        }

        return $ids;
    }
}
