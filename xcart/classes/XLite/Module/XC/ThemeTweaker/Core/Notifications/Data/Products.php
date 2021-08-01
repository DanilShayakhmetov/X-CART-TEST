<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\Core\Notifications\Data;


use XLite\Core\Cache\ExecuteCachedTrait;
use XLite\Core\Database;

class Products extends Provider
{
    use ExecuteCachedTrait;

    public function getData($templateDir)
    {
        return $this->getProducts($templateDir);
    }

    public function getName($templateDir)
    {
        return 'products';
    }

    public function validate($templateDir, $value)
    {
        $result = [];

        foreach (array_unique($this->prepareValue($value)) as $sku) {
            if (!$this->findProductBySku($sku)) {
                $result[] = [
                    'code'  => 'product_nf',
                    'value' => $sku,
                ];
            }
        }

        return $result;
    }

    public function isAvailable($templateDir)
    {
        return count($this->getProducts($templateDir)) > 0;
    }

    protected function getTemplateDirectories()
    {
        return [
        ];
    }

    /**
     * @param string $value
     *
     * @return array
     */
    protected function prepareValue($value)
    {
        return array_filter(preg_split('#(,|\s)#', $value), 'mb_strlen');
    }

    /**
     * @param string $templateDir
     *
     * @return []
     */
    protected function getProducts($templateDir)
    {
        return $this->executeCachedRuntime(function () use ($templateDir) {
            $products = \XLite\Core\Database::getRepo('XLite\Model\Product')->findBySku(
                $this->prepareValue($this->getValue($templateDir))
            );

            return $products
                ?: \XLite\Core\Database::getRepo('XLite\Model\Product')->findDumpProducts();
        });
    }

    /**
     * @param string $sku
     *
     * @return \XLite\Model\Product|null
     */
    protected function findProductBySku($sku)
    {
        return Database::getRepo('XLite\Model\Product')->findOneBySku($sku);
    }
}