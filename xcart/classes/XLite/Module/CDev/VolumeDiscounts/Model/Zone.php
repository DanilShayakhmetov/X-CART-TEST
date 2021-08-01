<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\VolumeDiscounts\Model;

/**
 * Zone
 */
abstract class Zone extends \XLite\Model\Zone implements \XLite\Base\IDecorator
{
    /**
     * Volume discounts
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ManyToMany (targetEntity="XLite\Module\CDev\VolumeDiscounts\Model\VolumeDiscount", mappedBy="zones")
     */
    protected $volumeDiscounts;

    /**
     * Add volume discount
     *
     * @param \XLite\Module\CDev\VolumeDiscounts\Model\VolumeDiscount $volumeDiscount
     * @return Membership
     */
    public function addVolumeDiscount(\XLite\Module\CDev\VolumeDiscounts\Model\VolumeDiscount $volumeDiscount)
    {
        $this->volumeDiscounts[] = $volumeDiscount;
        return $this;
    }

    /**
     * Get volume discounts
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getVolumeDiscounts()
    {
        return $this->volumeDiscounts;
    }
}
