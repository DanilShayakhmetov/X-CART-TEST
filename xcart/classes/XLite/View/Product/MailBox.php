<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Product;


use XLite\Model\Product;
use XLite\Model\WidgetParam\TypeInt;
use XLite\Model\WidgetParam\TypeObject;

class MailBox extends \XLite\View\AView
{
    const PARAM_PRODUCT               = 'product';
    const PARAM_PRODUCT_URL_PROCESSOR = 'productUrlProcessor';
    const PARAM_AMOUNT                = 'amount';

    protected function getDefaultTemplate()
    {
        return 'product/mailbox/body.twig';
    }

    public function getCSSFiles()
    {
        return array_merge(parent::getCSSFiles(), [
            'product/mailbox/style.less',
        ]);
    }

    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_PRODUCT               => new TypeObject('Product', null),
            static::PARAM_PRODUCT_URL_PROCESSOR => new TypeObject('Product url processor', $this->getDefaultProductURLProcessor()),
            static::PARAM_AMOUNT                => new TypeInt('Amount', null),
        ];
    }

    /**
     * @return Product
     */
    protected function getProduct()
    {
        return $this->getParam(static::PARAM_PRODUCT);
    }

    /**
     * @return \Closure
     */
    protected function getProductURLProcessor()
    {
        return $this->getParam(static::PARAM_PRODUCT_URL_PROCESSOR);
    }

    /**
     * @return string
     */
    protected function getProductURL()
    {
        $closure = $this->getProductURLProcessor();
        return $closure($this->getProduct());
    }

    /**
     * @return string
     */
    protected function getDefaultProductURLProcessor()
    {
        return function (Product $product) {
            return $this->buildFullURL('product', '', ['product_id' => $product->getId()]);
        };
    }

    /**
     * @return int
     */
    protected function getIconHeight()
    {
        return 280;
    }

    /**
     * @return int
     */
    protected function getIconWidth()
    {
        return 260;
    }

    /**
     * Return the icon 'alt' value
     *
     * @return string
     */
    protected function getIconAlt()
    {
        $product = $this->getProduct();

        return $product->getImage() && $product->getImage()->getAlt()
            ? $product->getImage()->getAlt()
            : $product->getName();
    }

    /**
     * @return int
     */
    protected function getLeftInStock()
    {
        return (integer)$this->getParam(static::PARAM_AMOUNT);
    }

    /**
     * @return bool
     */
    protected function isDisplayPrice()
    {
        return is_null($this->getParam(static::PARAM_AMOUNT));
    }

    /**
     * @return bool
     */
    protected function isDisplayLeftInStock()
    {
        return !is_null($this->getParam(static::PARAM_AMOUNT));
    }
}