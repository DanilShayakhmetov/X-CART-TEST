<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Reviews\View\Button;

/**
 * Login form in popup
 */
class PopupLogin extends \XLite\View\Button\PopupLogin
{
    const PARAM_PRODUCT = 'product';

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            self::PARAM_PRODUCT => new \XLite\Model\WidgetParam\TypeObject('Product', null, false, '\XLite\Model\Product'),
        ];
    }

    /**
     * Get product
     *
     * @return \XLite\Model\Product
     */
    protected function getProduct()
    {
        return $this->getParam(self::PARAM_PRODUCT);
    }

    /**
     * Return URL parameters to use in AJAX popup
     *
     * @return array
     */
    protected function prepareURLParams()
    {
        $productId = $this->getProduct() ? $this->getProduct()->getProductId() : null;

        if ($this->getTarget() == 'product_reviews') {
            $url = $this->buildURL('product_reviews', '', ['product_id' => $productId]);
        } else {
            $url = $this->buildURL('product', '', ['product_id' => $productId]) . '#product-details-tab-reviews';
        }

        return array_merge(parent::prepareURLParams(), [
            'widget'     => '\XLite\Module\XC\Reviews\View\AddReviewAuthorization',
            'product_id' => $productId,
            'fromURL'    => $url,
            'popup'      => '1',
        ]);
    }

    /**
     * Return list of JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'modules/XC/Reviews/button/popup_login/script.js';

        return $list;
    }
}