<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\creator\wishlist\Core;
use XLite\Core\Converter;
use XLite\Core\Session;

/**
 * Data class
 *
 */
class Data extends \XLite\Base\Singleton
{
    /**
     * Recently updated comparison time to live in sec
     */
    const RECENTLY_UPDATED_TTL = 3600;

    /**
     * Products count
     *
     * @var integer
     */
    protected $productsCount;

    /**
     * Product ids
     *
     * @var array
     */
    protected $productIds;

    /**
     * Get products count
     *
     * @return integer
     */
    public function getProductsCount()
    {
        if (!isset($this->productsCount)) {
            $this->productsCount = count($this->getProducts());
        }

        return $this->productsCount;
    }

    /**
     * Add product id
     *
     * @param integer $productId Product id
     *
     * @return void
     */
    public function addProductId($productId)
    {
        $ids = $this->getProductIds();
        $ids[$productId] = $productId;
        $this->productIds = $ids;
        Session::getInstance()->productWishlistIds = $ids;
        $this->renewUpdatedTime();
    }

    /**
     * Delete product id
     *
     * @param integer $productId Product id
     *
     * @return void
     */
    public function deleteProductId($productId)
    {
        $ids = $this->getProductIds();
        if (isset($ids[$productId])) {
            unset($ids[$productId]);
        }
        $this->productIds = $ids;
        Session::getInstance()->productWishlistIds = $ids;
    }

    /**
     * Clear list
     *
     * @return void
     */
    public function clearList()
    {
        $this->productIds = array();
        Session::getInstance()->productWishlistIds = array();
    }

    /**
     * Check if recently updated
     *
     * @return boolean
     */
    public function isRecentlyUpdated()
    {
        if ($time = $this->getUpdatedTime()) {
            return $time > (Converter::getInstance()->time() - static::RECENTLY_UPDATED_TTL);
        }

        return false;
    }

    /**
     * Return updated time
     *
     * @return integer
     */
    public function getUpdatedTime()
    {
        return Session::getInstance()->productWishlistUpdatedTime;
    }

    /**
     * Renew updated time
     */
    public function renewUpdatedTime()
    {
        Session::getInstance()->productWishlistUpdatedTime = Converter::getInstance()->time();
    }

    /**
     * Set updated time to 0
     */
    public function unsetUpdatedTime()
    {
        Session::getInstance()->productWishlistUpdatedTime = 0;
    }

    /**
     * Get product ids
     *
     * @return array
     */
    public function getProductIds()
    {
        if (!isset($this->productIds)) {
            $this->productIds = Session::getInstance()->productWishlistIds;
        }

        return is_array($this->productIds)
            ? $this->productIds
            : array();
    }

    /**
     * Get products
     *
     * @return array
     */
    public function getProducts()
    {
        $ids = $this->getProductIds();

        return !empty($ids)
            ? \XLite\Core\Database::getRepo('\XLite\Model\Product')->findByIds($this->getProductIds())
            : [];
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        $count = $this->getProductsCount();

        return 1 >= $count
            ? static::t('Add other products to wishlist')
            : static::t(
                'X products selected',
                array(
                    'count' => $count
                )
            );
    }

}
