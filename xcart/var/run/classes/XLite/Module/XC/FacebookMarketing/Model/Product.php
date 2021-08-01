<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\FacebookMarketing\Model;


/**
 * The "product" model class
 */
 class Product extends \XLite\Module\XC\FreeShipping\Model\Product implements \XLite\Base\IDecorator
{
    /**
     * Product is available for Facebook Marketing feed
     *
     * @var boolean
     *
     * @Column (type="boolean", options={"default" : true})
     */
    protected $facebookMarketingEnabled = true;

    /**
     * Return FacebookMarketingEnabled
     *
     * @return boolean
     */
    public function getFacebookMarketingEnabled()
    {
        return $this->facebookMarketingEnabled;
    }

    /**
     * Set FacebookMarketingEnabled
     *
     * @param boolean $facebookMarketingEnabled
     *
     * @return $this
     */
    public function setFacebookMarketingEnabled($facebookMarketingEnabled)
    {
        $this->facebookMarketingEnabled = $facebookMarketingEnabled;
        return $this;
    }

    /**
     * Return product identifier for facebook pixel
     *
     * @return string
     */
    public function getFacebookPixelProductIdentifier()
    {
        return $this->getSku();
    }
}