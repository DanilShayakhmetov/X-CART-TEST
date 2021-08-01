<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\FileAttachments\View\Product;

/**
 * Product attachments tab
 */
class Admin extends \XLite\View\Tabs\ATabs
{
    /**
     * Common widget parameter names
     */
    const PARAM_PRODUCT = 'product';

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'common/tabs2.twig';
    }

    /**
     * @return array
     */
    protected function defineTabs()
    {
        return [
            'attachments' => [
                'weight'   => 100,
                'title'    => static::t('Attachments'),
                'template' => 'modules/CDev/FileAttachments/product.twig',
            ],
        ];
    }

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'modules/CDev/FileAttachments/admin.js';

        return $list;
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/CDev/FileAttachments/admin.less';

        return $list;
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
            self::PARAM_PRODUCT => new \XLite\Model\WidgetParam\TypeObject('Product', null, false, 'XLite\Model\Product'),
        ];
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
        && $this->getProduct()
        && $this->getProduct()->getProductId();
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
     * Returns an URL to a tab
     *
     * @param string $target Tab target
     *
     * @return string
     */
    protected function buildTabURL($target)
    {
        $productId = $this->getProductId();

        return $this->buildURL('product', '', [
            'product_id' => $productId,
            'page'       => 'attachments',
            'subpage'    => $target,
        ]);
    }

    /**
     * Returns the current target
     *
     * @return string
     */
    protected function getCurrentTarget()
    {
        return \XLite\Core\Request::getInstance()->subpage ?: $this->getDefaultTabIndex();
    }

    /**
     * Return default tab index
     *
     * @return string
     */
    protected function getDefaultTabIndex()
    {
        return 'attachments';
    }
}

