<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\Core\Notifications\Data;


use XLite\Core\Cache\ExecuteCachedTrait;
use XLite\Core\Database;

class Product extends Provider
{
    use ExecuteCachedTrait;

    public function getData($templateDir)
    {
        return $this->getProduct($templateDir);
    }

    public function getName($templateDir)
    {
        return 'product';
    }

    public function validate($templateDir, $value)
    {
        if (!$this->findProductBySku($value)) {
            return [
                [
                    'code' => 'product_nf',
                    'value' => $value
                ],
            ];
        }

        return [];
    }

    public function isAvailable($templateDir)
    {
        return !!$this->getProduct($templateDir);
    }

    protected function getTemplateDirectories()
    {
        return [
            'low_limit_warning'
        ];
    }

    /**
     * @param string $templateDir
     *
     * @return \XLite\Model\Product|null
     */
    protected function getProduct($templateDir)
    {
        return $this->executeCachedRuntime(function () use ($templateDir) {
            return $this->findProductBySku($this->getValue($templateDir))
                ?: \XLite\Core\Database::getRepo('XLite\Model\Product')->findDumpProduct();
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