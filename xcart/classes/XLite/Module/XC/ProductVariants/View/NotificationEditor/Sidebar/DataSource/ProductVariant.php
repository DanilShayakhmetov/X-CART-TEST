<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductVariants\View\NotificationEditor\Sidebar\DataSource;


use XLite\Module\XC\ThemeTweaker\Core\Notifications\Data;
use XLite\Module\XC\ThemeTweaker\View\NotificationEditor\Sidebar\DataSource\DataSource;
use XLite\View\AView;

class ProductVariant extends AView implements DataSource
{
    private $data;

    protected function getDefaultTemplate()
    {
        return 'modules/XC/ProductVariants/notification_editor/sidebar/data_source/product_variant/body.twig';
    }

    static public function isApplicable(Data $data)
    {
        return in_array(
            $data->getDirectory(),
            static::getTemplateDirectories(),
            true
        );
    }

    protected static function getTemplateDirectories()
    {
        return [
            'modules/XC/ProductVariants/low_variant_limit_warning',
        ];
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
     * @return \XLite\Module\XC\ProductVariants\Model\ProductVariant|null
     */
    protected function getProductVariant()
    {
        return isset($this->data->getData()['product_variant'])
            ? $this->data->getData()['product_variant']
            : null;
    }

    /**
     * @return string
     */
    protected function getValue()
    {
        return $this->getProductVariant()
            ? $this->getProductVariant()->getVariantId()
            : '';
    }
}