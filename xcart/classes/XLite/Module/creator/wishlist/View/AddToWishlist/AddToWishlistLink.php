<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\creator\wishlist\View\AddToWishlist;

use XLite\Module\creator\wishlist\Core\Data;

/**
 * Product comparison widget
 *
 *  @ListChild (list="slidebar.additional-menu.links", zone="customer", weight="30")
 */
class AddToWishlistLink extends \XLite\View\Container
{

    /**
     * Checkbox id
     *
     * @var string
     */
    protected $checkboxId;

    /**
     * Product id
     *
     * @var string
     */
    protected $productId;

    /**
     * Get checkbox id
     *
     * @param integer $productId Product id
     *
     * @return string
     */
    public function getCheckboxId($productId)
    {
        if (null === $this->checkboxId
            || (int) $productId !== $this->productId
        ) {
            $this->checkboxId = 'product' . mt_rand() . $productId;
        }

        $this->productId = (int) $productId;

        return $this->checkboxId;
    }

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'modules/XC/ProductComparison/header_widget.js';

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
        $list[] = array(
            'file'  => 'modules/XC/ProductComparison/header_widget.less',
            'media' => 'screen',
            'merge' => 'bootstrap/css/bootstrap.less',
        );

        return $list;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return \XLite\Module\XC\ProductComparison\Core\Data::getInstance()->getTitle();
    }

    /**
     * Is checked
     *
     * @param integer $productId Product id
     *
     * @return boolean
     */
    public function isChecked($productId)
    {
        $ids = \XLite\Module\XC\ProductComparison\Core\Data::getInstance()->getProductIds();

        return $ids && isset($ids[$productId]);
    }

    /**
     * Is empty
     *
     * @return boolean
     */
    protected function isEmptyList()
    {
        return 0 === \XLite\Module\XC\ProductComparison\Core\Data::getInstance()->getProductsCount();
    }
    /**
     * Return default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . '/header_settings_link.twig';
    }

    /**
     * Get widget templates directory
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/creator/wishlist';
    }

    /**
     * Check if recently updated
     *
     * @return boolean
     */
    protected function isRecentlyUpdated()
    {
        return Data::getInstance()->isRecentlyUpdated();
    }

    /**
     * Return compare url
     *
     * @return string
     */
    protected function getCompareURL()
    {
        return $this->buildURL('wishlist');
    }

    /**
     * Check if disabled
     *
     * @return bool
     */
    protected function isDisabled()
    {
        return $this->getComparedCount() < 2;
    }

    /**
     * Return list of indicator element classes
     *
     * @return array
     */
    protected function getIndicatorClassesList()
    {
        $list = [];

        if ($this->isDisabled()) {
            $list[] = 'disabled';
        }

        if ($this->getComparedCount() > 0 && $this->isRecentlyUpdated()) {
            $list[] = 'recently-updated';
        }

        return $list;
    }

    /**
     * Return compared count
     *
     * @return int
     */
    protected function getComparedCount()
    {
        return Data::getInstance()->getProductsCount();
    }

    /**
     * Return title message
     *
     * @return string
     */
    protected function getLinkHelpMessage()
    {
        return $this->isDisabled()
            ? static::t('Please add another product to comparison')
            : static::t('Go to comparison table');
    }

    /**
     * Return indicator element classes
     *
     * @return string
     */
    protected function getIndicatorClasses()
    {
        return implode(' ', $this->getIndicatorClassesList());
    }
}
