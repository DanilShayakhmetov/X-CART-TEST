<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductVariants\Core\Notifications\Data;


use XLite\Core\Cache\ExecuteCachedTrait;
use XLite\Core\Database;
use XLite\Module\XC\ThemeTweaker\Core\Notifications\Data\Provider;

class ProductVariant extends Provider
{
    use ExecuteCachedTrait;

    public function getData($templateDir)
    {
        return $this->getProductVariant($templateDir);
    }

    public function getName($templateDir)
    {
        return 'product_variant';
    }

    public function validate($templateDir, $value)
    {
        if (!$this->findProductVariantById($value)) {
            return [
                [
                    'code'  => 'variant_nf',
                    'value' => $value,
                ],
            ];
        }

        return [];
    }

    public function isAvailable($templateDir)
    {
        return (boolean)$this->getProductVariant($templateDir);
    }

    protected function getTemplateDirectories()
    {
        return [
            'modules/XC/ProductVariants/low_variant_limit_warning',
        ];
    }

    /**
     * @param $templateDir
     *
     * @return \XLite\Module\XC\ProductVariants\Model\ProductVariant|null
     */
    protected function getProductVariant($templateDir)
    {
        return $this->executeCachedRuntime(function () use ($templateDir) {
            return $this->findProductVariantById($this->getValue($templateDir))
                ?: Database::getRepo('XLite\Module\XC\ProductVariants\Model\ProductVariant')
                    ->findDumpProductVariant();
        });
    }

    /**
     * @param string $variantId
     *
     * @return null|\XLite\Module\XC\ProductVariants\Model\ProductVariant
     */
    protected function findProductVariantById($variantId)
    {
        return Database::getRepo('XLite\Module\XC\ProductVariants\Model\ProductVariant')->findOneBy([
            'variant_id' => $variantId,
        ]);
    }
}