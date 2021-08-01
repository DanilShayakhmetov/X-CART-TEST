<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ItemsList\Product;

use XLite\Model\WidgetParam\TypeCollection;
use XLite\Model\WidgetParam\TypeObject;
use XLite\Model\WidgetParam\TypeString;
use XLite\View\Product\MailBox as MailBoxItem;

class MailBox extends \XLite\View\AView
{
    const PARAM_PRODUCTS       = 'products';
    const PARAM_PRODUCT_WIDGET = 'productWidget';

    protected function getDefaultTemplate()
    {
        return 'items_list/product/mailbox/body.twig';
    }

    protected function getCommonFiles()
    {
        return array_merge_recursive(parent::getCommonFiles(), [
            static::RESOURCE_CSS => [
                'items_list/product/mailbox/style.less',
            ],
        ]);
    }

    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_PRODUCTS                   => new TypeCollection('Products', []),
            static::PARAM_PRODUCT_WIDGET             => new TypeString('Product widget', '\XLite\View\Product\MailBox'),
            MailBoxItem::PARAM_PRODUCT_URL_PROCESSOR => new TypeObject('Product url processor', null),
        ];
    }

    /**
     * @return array
     */
    protected function getProducts()
    {
        return $this->getParam(static::PARAM_PRODUCTS);
    }

    /**
     * @return array
     */
    protected function getProductWidget()
    {
        return $this->getParam(static::PARAM_PRODUCT_WIDGET);
    }

    /**
     * @return \Closure|null
     */
    protected function getUrlProcessor()
    {
        return $this->getParam(MailBoxItem::PARAM_PRODUCT_URL_PROCESSOR);
    }
}