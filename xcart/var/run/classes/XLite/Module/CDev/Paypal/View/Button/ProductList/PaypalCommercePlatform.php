<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\View\Button\ProductList;

use XLite\Model\WidgetParam\TypeInt;

/**
 * Paypal Commerce Platform product list button
 */
class PaypalCommercePlatform extends \XLite\Module\CDev\Paypal\View\Button\APaypalCommercePlatform
{
    /**
     * Widget parameters
     */
    const PARAM_PRODUCT_ID = 'productId';

    /**
     * Returns true if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible() && \XLite\Module\CDev\Paypal\Main::isBuyNowEnabled();
    }

    /**
     * @return array
     */
    public function getJSFiles()
    {
        return array_merge(parent::getJSFiles(), [
            'modules/CDev/Paypal/button/paypal_commerce_platform/product_list.js',
        ]);
    }

    /**
     * @return string
     */
    protected function getButtonClass()
    {
        return parent::getButtonClass() . ' pcp-product-list add-to-cart-button';
    }

    /**
     * @return string
     */
    protected function getButtonStyleNamespace()
    {
        return 'product_list';
    }

    /**
     * @return string
     */
    protected function getButtonLayout()
    {
        return 'horizontal';
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            self::PARAM_PRODUCT_ID => new TypeInt('ProductId'),
        ];
    }

    /**
     * Get associated product's id.
     *
     * @return int
     */
    protected function getProductId()
    {
        return $this->getParam(self::PARAM_PRODUCT_ID);
    }
}
