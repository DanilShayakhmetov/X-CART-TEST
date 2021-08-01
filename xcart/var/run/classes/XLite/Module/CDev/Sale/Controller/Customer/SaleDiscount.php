<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Sale\Controller\Customer;

/**
 * Sale
 */
class SaleDiscount extends \XLite\Controller\Customer\ACustomer
{
    protected $id;

    /**
     * @return int|null
     */
    public function getSaleDiscountId()
    {
        return \XLite\Core\Request::getInstance()->id;
    }

    /**
     * Check whether the title is to be displayed in the content area
     *
     * @return boolean
     */
    public function isTitleVisible()
    {
        return $this->isVisible();
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->isVisible() 
            ? $this->getSaleDiscount()->getName()
            : static::t('Page not found');
    }

    /**
     * Common method to determine current location
     *
     * @return string
     */
    protected function getLocation()
    {
        return $this->getTitle();
    }


    /**
     * Returns the page title (for the <title> tag)
     *
     * @return string
     */
    public function getTitleObjectPart()
    {
        $discount = $this->getSaleDiscount();

        return ($discount && $discount->getMetaTitle()) ? $discount->getMetaTitle() : $this->getTitle();
    }

    /**
     * Get meta description
     *
     * @return string
     */
    public function getMetaDescription()
    {
        $discount = $this->getSaleDiscount();

        return $discount ? $discount->getMetaDesc() : parent::getMetaDescription();
    }

    /**
     * Get meta keywords
     *
     * @return string
     */
    public function getKeywords()
    {
        $discount = $this->getSaleDiscount();

        return $discount ? $discount->getMetaTags() : parent::getKeywords();
    }


    /**
     * Define and set handler attributes; initialize handler
     *
     * @param array $params Handler params OPTIONAL
     */
    public function __construct(array $params = array())
    {
        parent::__construct();

        $this->params[] = 'id';
        $this->id = $this->getSaleDiscountId();
    }

    /**
     * Returns sale discount
     *
     * @return \XLite\Module\CDev\Sale\Model\SaleDiscount
     */
    public function getSaleDiscount()
    {
        $discountId = $this->getSaleDiscountId();
        return $this->executeCachedRuntime(function() use ($discountId) {
            return \XLite\Core\Database::getRepo('XLite\Module\CDev\Sale\Model\SaleDiscount')
                    ->find($discountId);
        }, ['getSaleDiscount', $discountId]);
    }

    /**
     * Check controller visibility
     *
     * @return boolean
     */
    protected function isVisible()
    {
        $profile = null;

        $profile = $this->getCart(true)->getProfile()
            ?: \XLite\Core\Auth::getInstance()->getProfile();

        if (!$profile) {
            $profile = new \XLite\Model\Profile();
        }
        
        return parent::isVisible()
            && null !== $this->getSaleDiscount()
            && $this->getSaleDiscount()->getShowInSeparateSection()
            && $this->getSaleDiscount()->isActive()
            && $this->getSaleDiscount()->isApplicableForProfile($profile);
    }
}
