<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Wholesale\View;

use Includes\Utils\Module\Manager;

/**
 * Product page widgets collection
 */
class ProductPageCollection extends \XLite\View\ProductPageCollection implements \XLite\Base\IDecorator
{
    /**
     * Register the view classes collection
     *
     * @return array
     */
    protected function defineWidgetsCollection()
    {
        return array_merge(
            parent::defineWidgetsCollection(),
            [
                'XLite\Module\CDev\Wholesale\View\ProductPrice',
                'XLite\View\Product\Details\Customer\EditableAttributes',
            ]
        );
    }

    /**
     * Check - allowed display subwidget or not
     *
     * @param string $name Widget class name
     *
     * @return boolean
     */
    protected function isAllowedWidget($name)
    {
        $result = parent::isAllowedWidget($name);

        if ($result) {
            switch ($name) {
                case '\XLite\Module\CDev\Wholesale\View\ProductPrice':
                    $types = $this->getProductModifierTypes();
                    if (empty($types['wholesalePrice'])) {
                        $result = false;
                    }
                    break;

                default:
            }
        }

        return $result;
    }

    /**
     * Get product modifier types
     *
     * @return array
     */
    protected function getProductModifierTypes()
    {
        $additional = null;
        $additionalVariants = null;
        $wholesale = null;

        if (!isset($this->productModifierTypes)) {
            $variantsEnabled = false;
            if (Manager::getRegistry()->isModuleEnabled('XC', 'ProductVariants')) {
                $variantsEnabled = true;
                // ProductVariants module detected
                $additional = \XLite\Core\Database::getRepo('XLite\Module\XC\ProductVariants\Model\ProductVariant')
                    ->getModifierTypesByProduct($this->getProduct());
                $additionalVariants = \XLite\Core\Database::getRepo('XLite\Module\CDev\Wholesale\Model\ProductVariantWholesalePrice')
                    ->getModifierTypesByProduct($this->getProduct());
            }
            if (empty($additional['price']) || empty($additionalVariants['price']) || empty($additionalVariants['wholesalePrice'])) {
                // ProductVariants module is not detected or product has not variants
                $wholesale = \XLite\Core\Database::getRepo('XLite\Module\CDev\Wholesale\Model\WholesalePrice')
                    ->getModifierTypesByProduct($this->getProduct());
            }
        }

        $result = parent::getProductModifierTypes();

        foreach ([$additional, $additionalVariants, $wholesale] as $modifierTypes) {

            if (isset($modifierTypes)) {

                foreach ($modifierTypes as $key => $value) {
                    $result[$key] = isset($result[$key])
                        ? $result[$key] || $value
                        : $value;
                }

                if (!$result['price'] && $modifierTypes['price']) {
                    $result['price'] = true;
                }

                $this->productModifierTypes = $result;
            }
        }

        return $result;
    }
}
