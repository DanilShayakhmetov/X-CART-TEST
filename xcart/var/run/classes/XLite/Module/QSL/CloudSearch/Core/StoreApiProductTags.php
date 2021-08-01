<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\CloudSearch\Core;

use XLite\Model\Product;
use XLite\Module\XC\ProductTags\Model\Tag;


/**
 * CloudSearch store-side API methods
 *
 * @Decorator\Depend ({"XC\ProductTags"})
 */
 class StoreApiProductTags extends \XLite\Module\QSL\CloudSearch\Core\StoreApiReviews implements \XLite\Base\IDecorator
{
    /**
     * Get single product data
     *
     * @param Product $product
     *
     * @return array
     */
    public function getProduct(Product $product)
    {
        $tagValues = $this->getTagValues($product);

        $tags = [];
        foreach ($this->getActiveLanguages() as $lang) {
            $tags["tags_$lang"] = isset($tagValues[$lang]) ? $tagValues[$lang] : [];
        }

        return parent::getProduct($product) + $tags;
    }

    /**
     * Get filterable product attributes data
     *
     * @param $product
     *
     * @return array
     */
    protected function getFilterableProductAttributes(Product $product)
    {
        $attributes = parent::getFilterableProductAttributes($product);

        $tagValues = $this->getTagValues($product);

        if ($tagValues) {
            $attribute = [
                'id'                => 'XC\ProductTags',
                'preselectAsFilter' => true,
                'group'             => 'Product Tags module',
            ];

            foreach ($this->getActiveLanguages() as $lang) {
                $attribute["name_$lang"] = (string) static::t('Tags', [], $lang);
                $attribute["values_$lang"] = isset($tagValues[$lang]) ? $tagValues[$lang] : [];
            }

            $attributes[] = $attribute;
        }

        return $attributes;
    }

    /**
     * Get product's tag values
     *
     * @param Product $product
     *
     * @return array
     */
    protected function getTagValues(Product $product)
    {
        $tags = $product->getTags();

        $tagValues = [];
        /** @var Tag $tag */
        foreach ($tags as $tag) {
            $tagTranslations = [];
            foreach ($tag->getTranslations() as $t) {
                if (isset($tagTranslations[$t->getCode()])) {
                    continue;
                }

                $tagTranslations[$t->getCode()] = $t->getName();
            }

            foreach ($this->getActiveLanguages() as $lang) {
                if (!isset($tagValues[$lang])) {
                    $tagValues[$lang] = [];
                }

                $tagValues[$lang][] = $this->getFieldTranslation($tagTranslations, $lang);
            }
        }

        return $tagValues;
    }
}
