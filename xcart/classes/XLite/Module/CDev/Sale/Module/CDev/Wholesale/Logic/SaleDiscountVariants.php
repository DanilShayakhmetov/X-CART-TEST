<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Sale\Module\CDev\Wholesale\Logic;

use XLite\Core\Database;

/**
 * Net price modificator: price with sale discount
 *
 * @Decorator\Depend ({"CDev\Wholesale","XC\ProductVariants"})
 */
class SaleDiscountVariants extends \XLite\Module\CDev\Sale\Logic\SaleDiscount implements \XLite\Base\IDecorator
{
    protected static $wholesaleProductVariants = [];

    static protected function isSaleDiscountApplicable(\XLite\Module\CDev\Sale\Model\SaleDiscount $saleDiscount, $model)
    {
        $result = parent::isSaleDiscountApplicable($saleDiscount, $model);

        $object = static::getObject($model);
        if (
            $result
            && $object instanceof \XLite\Module\XC\ProductVariants\Model\ProductVariant
        ) {
            $controller = \XLite::getController();
            $profile = null;

            if ($controller instanceof \XLite\Controller\Customer\ACustomer) {
                $profile = $controller->getCart(true)->getProfile()
                    ?: \XLite\Core\Auth::getInstance()->getProfile();
            }

            if (!$profile) {
                $profile = new \XLite\Model\Profile();
            }

            $key = $object->getUniqueIdentifier() . $profile->getMembershipId();
            if (!isset(static::$wholesaleProductVariants[$key])) {
                $wholesalePrices = Database::getRepo('XLite\Module\CDev\Wholesale\Model\ProductVariantWholesalePrice')->getWholesalePrices(
                    $object,
                    $profile->getMembership()
                );

                if (empty($wholesalePrices)) {
                    $wholesalePrices = Database::getRepo('XLite\Module\CDev\Wholesale\Model\WholesalePrice')->getWholesalePrices(
                        $object->getProduct(),
                        $profile->getMembership()
                    );
                }

                static::$wholesaleProductVariants[$key] = !empty($wholesalePrices);
            }

            $result = !static::$wholesaleProductVariants[$key] || $saleDiscount->getApplyToWholesale();
        }

        return $result;
    }
}
