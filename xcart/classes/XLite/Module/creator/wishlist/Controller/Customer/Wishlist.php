<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
namespace XLite\Module\creator\wishlist\Controller\Customer;

/**
 * Wishlist
 *
 */
class Wishlist extends \XLite\Controller\Customer\ACustomer
{
    /**
     * Controller parameters
     *
     * @var array
     */
    protected $params = array('target');

    /**
     * Products
     *
     * @var array
     */
    protected $products;

    /**
     * Get page title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->isVisible()
            ? static::t(
                'Comparison table - X items',
                array(
                    'count' => \XLite\Module\creator\wishlist\Core\Data::getInstance()->getProductsCount()
                )
            ) : '';
    }


    /**
     * Get products
     *
     * @return array
     */
    public function getProducts()
    {
        if (!isset($this->products)) {
            $this->products = \XLite\Module\creator\wishlist\Core\Data::getInstance()->getProducts();
        }

        return $this->products;
    }

    /**
     * Product comparison delete
     *
     * @return void
     */
    protected function doActionDelete()
    {
        \XLite\Module\creator\wishlist\Core\Data::getInstance()->unsetUpdatedTime();
        $id = abs(intval(\XLite\Core\Request::getInstance()->product_id));
        if ($id) {
            $core = \XLite\Module\creator\wishlist\Core\Data::getInstance();
            $core->deleteProductId($id);
            $this->setReturnURL($this->buildURL('wishlist'));
        }
    }

    /**
     * Return products count
     *
     * @return int
     */
    public function getProductsCount()
    {
        return count($this->getProducts());
    }

    /**
     * Clear list
     *
     * @return void
     */
    protected function doActionClear()
    {
        \XLite\Module\creator\wishlist\Core\Data::getInstance()->unsetUpdatedTime();
        \XLite\Module\creator\wishlist\Core\Data::getInstance()->clearList();
        $this->setReturnURL($this->buildURL(''));
    }

    /**
     * Preprocessor for no-action run
     *
     * @return void
     */
    protected function doNoAction()
    {
        parent::doNoAction();

        \XLite\Module\creator\wishlist\Core\Data::getInstance()->unsetUpdatedTime();

        if (!$this->isAJAX()) {
            \XLite\Core\Session::getInstance()->productListURL = $this->getURL();
        }
    }
}
