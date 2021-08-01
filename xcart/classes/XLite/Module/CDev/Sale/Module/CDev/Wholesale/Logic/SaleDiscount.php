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
 * @Decorator\Depend ("CDev\Wholesale")
 */
class SaleDiscount extends \XLite\Module\CDev\Sale\Logic\SaleDiscount implements \XLite\Base\IDecorator
{
    protected static $wholesaleProducts = [];

    static protected function isSaleDiscountApplicable(\XLite\Module\CDev\Sale\Model\SaleDiscount $saleDiscount, $model)
    {
        $result = parent::isSaleDiscountApplicable($saleDiscount, $model);

        $object = static::getObject($model);
        if (
            $result
            && $object instanceof \XLite\Model\Product
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
            if (!isset(static::$wholesaleProducts[$key]) && $model->getWholesaleQuantity() > 1) {
                $wholesalePrices = Database::getRepo('XLite\Module\CDev\Wholesale\Model\WholesalePrice')->getWholesalePrices(
                    $object,
                    $profile->getMembership()
                );

                static::$wholesaleProducts[$key] = !empty($wholesalePrices);
            }

            $result = !static::$wholesaleProducts[$key] || $saleDiscount->getApplyToWholesale();
        }

        return $result;
    }
}
