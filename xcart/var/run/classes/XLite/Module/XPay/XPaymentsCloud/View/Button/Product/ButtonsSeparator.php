<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XPay\XPaymentsCloud\View\Button\Product;

/**
 * Product buttons separator
 *
 * @ListChild (list="product.details.page.info.form.buttons.cart-buttons", zone="customer", weight="125")
 * @ListChild (list="product.details.page.info.form.buttons-added.cart-buttons", zone="customer", weight="125")
 */
class ButtonsSeparator extends \XLite\Module\XPay\XPaymentsCloud\View\Button\AButtonsSeparator
{
    /**
     * Returns true if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        $availableForSale = false;
        $controller = \XLite::getController();
        if ($controller instanceof \XLite\Controller\Customer\Product) {
            $availableForSale = !$controller->getProduct()->isAllStockInCart() && $controller->getProduct()->isAvailable();
        }

        return parent::isVisible()
            && $availableForSale;
    }

}
