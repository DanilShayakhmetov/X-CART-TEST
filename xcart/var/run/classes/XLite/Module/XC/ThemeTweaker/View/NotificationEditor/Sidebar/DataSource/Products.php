<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\View\NotificationEditor\Sidebar\DataSource;


use XLite\Module\XC\ThemeTweaker\Core\Notifications\Data;
use XLite\View\AView;

class Products extends AView implements DataSource
{
    private $data;

    static public function isApplicable(Data $data)
    {
        return in_array(
            $data->getDirectory(),
            static::getTemplateDirectories(),
            true
        );
    }

    public function __construct(Data $data)
    {
        $this->data = $data;
        parent::__construct([]);
    }

    static public function buildNew(Data $data)
    {
        return new static($data);
    }

    /**
     * @return array
     */
    protected static function getTemplateDirectories()
    {
        return [
        ];
    }

    protected function getDefaultTemplate()
    {
        return 'modules/XC/ThemeTweaker/notification_editor/sidebar/data_source/products/body.twig';
    }

    /**
     * @return \XLite\Model\Product[]
     */
    protected function getProducts()
    {
        return !empty($this->data->getData()['products'])
            ? $this->data->getData()['products']
            : null;
    }

    /**
     * @param array $products
     *
     * @return string
     */
    protected function prepareSkus(array $products)
    {
        return implode(' ', array_map(function (\XLite\Model\Product $product) {
            return '#' . $product->getSku();
        }, $products));
    }

    /**
     * @return string
     */
    protected function getValue()
    {
        return $this->getProducts()
            ? $this->prepareSkus($this->getProducts())
            : '';
    }
}