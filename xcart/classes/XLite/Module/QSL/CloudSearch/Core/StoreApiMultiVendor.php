<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\CloudSearch\Core;

use XLite\Model\Product;
use XLite\Module\XC\MultiVendor\Model\Product as MultiVendorProduct;
use XLite\Module\XC\MultiVendor\Model\Profile as MultiVendorProfile;


/**
 * CloudSearch store-side API methods
 *
 * @Decorator\Depend ({"XC\MultiVendor"})
 */
class StoreApiMultiVendor extends \XLite\Module\QSL\CloudSearch\Core\StoreApi implements \XLite\Base\IDecorator
{
    /**
     * Get filterable product attributes data
     *
     * @param $product
     *
     * @return array
     */
    protected function getFilterableProductAttributes(Product $product)
    {
        $codes = $this->getActiveLanguages();

        $attributes = parent::getFilterableProductAttributes($product);

        $vendorTranslations = $this->getVendorTranslations($product);

        if (!$vendorTranslations) {
            return $attributes;
        }

        $vendorAttrValues = [];
        foreach ($codes as $code) {
            $vendorAttrValues["values_{$code}"] = [$vendorTranslations[$code]];
            $vendorAttrValues["name_{$code}"] = (string) static::t('Vendor', [], $code);
        }

        $attributes[] = [
                'id'                => 'XC\MultiVendor',
                'preselectAsFilter' => true,
                'group'             => 'Multi-vendor module',
            ] + $vendorAttrValues;

        return $attributes;
    }

    /**
     * Get searchable product attributes data
     *
     * @param $product
     *
     * @return array
     */
    protected function getSearchableProductAttributes(Product $product)
    {
        $codes = $this->getActiveLanguages();

        $attributes = parent::getSearchableProductAttributes($product);

        $vendorTranslations = $this->getVendorTranslations($product);

        if (!$vendorTranslations) {
            return $attributes;
        }

        $vendorAttr = [];
        foreach ($codes as $code) {
            $vendorAttr["name_{$code}"] = (string) static::t('Vendor', [], $code);
            $vendorAttr["values_{$code}"] = [$vendorTranslations[$code]];
        }

        $attributes[] = $vendorAttr;

        return $attributes;
    }

    /**
     * Get "conditions" that can be used to restrict the results when searching.
     *
     * This is different from "attributes" which are used to construct full-fledged filters (CloudFilters).
     *
     * @param Product $product
     * @return array
     */
    protected function getProductConditions(Product $product)
    {
        $conditions = parent::getProductConditions($product);

        /** @var MultiVendorProduct $product */
        $vendor = $product->getVendor();

        $vendorId = $vendor !== null ? $vendor->getProfileId() : 0;

        $conditions['vendor'] = [$vendorId];

        return $conditions;
    }

    protected function getVendorTranslations(Product $product)
    {
        $activeLanguages = $this->getActiveLanguages();

        /** @var MultiVendorProduct $product */
        /** @var MultiVendorProfile $vendor */
        $vendor = $product->getVendor();

        $vendorTranslations = [];
        if ($vendor !== null) {
            foreach ($vendor->getVendor()->getTranslations() as $t) {
                if (isset($vendorTranslations[$t->getCode()])) {
                    continue;
                }

                $vendorTranslations[$t->getCode()] = $t->getCompanyName();
            }

            foreach ($activeLanguages as $lang) {
                $vendorTranslations[$lang] = $this->getFieldTranslation($vendorTranslations, $lang);
            }
        } else {
            foreach ($activeLanguages as $lang) {
                $vendorTranslations[$lang] = (string) static::t('Main vendor', [], $lang);
            }
        }

        return array_filter($vendorTranslations);
    }

    /**
     * Get sort fields that can be used to sort CloudSearch search results.
     * Sort fields are dynamic in the way that custom sort_int_*, sort_float_*, sort_str_* are allowed.
     *
     * @param Product $product
     *
     * @return array
     */
    protected function getSortFields(Product $product)
    {
        return parent::getSortFields($product) + ['sort_str_vendor' => $product->getVendorLogin() ?: ''];
    }
}
