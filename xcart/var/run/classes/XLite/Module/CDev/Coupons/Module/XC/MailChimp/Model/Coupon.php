<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Coupons\Module\XC\MailChimp\Model;


use XLite\Module\CDev\Coupons\Module\XC\MailChimp\Core\Action\CouponUpdate;
use XLite\Module\XC\MailChimp\Core\MailChimpECommerce;
use XLite\Module\XC\MailChimp\Core\MailChimpQueue;
use XLite\Module\XC\MailChimp\Main;

/**
 * Generator
 *
 * @Decorator\Depend("XC\MailChimp")
 */
 class Coupon extends \XLite\Module\CDev\Coupons\Model\CouponAbstract implements \XLite\Base\IDecorator
{
    /**
     * @PostPersist
     */
    public function mailChimpPostPersist()
    {
        if (Main::isMailChimpECommerceConfigured() && Main::getMainStores()) {
            foreach (Main::getMainStores() as $store) {
                MailChimpECommerce::getInstance()->createCoupon($store->getId(), $this);
            }
        }
    }

    /**
     * @PreUpdate
     */
    public function mailChimpPreUpdate()
    {
        $changeSet = \XLite\Core\Database::getEM()->getUnitOfWork()->getEntityChangeSet($this);

        if (Main::isMailChimpECommerceConfigured() && Main::getMainStores()
            && $this->getId()
            && array_filter($changeSet)
        ) {
            MailChimpQueue::getInstance()->addAction(
                'couponUpdate' . $this->getId(),
                new CouponUpdate($this)
            );
        }
    }

    /**
     * @PreRemove
     */
    public function mailChimpPreRemove()
    {
        if (Main::isMailChimpECommerceConfigured() && Main::getMainStores()) {
            foreach (Main::getMainStores() as $store) {
                MailChimpECommerce::getInstance()->removeCoupon(
                    $store->getId(),
                    $this->getId()
                );
            }
        }
    }
}