<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Coupons\Module\XC\MailChimp\Core\Action;


use XLite\Module\XC\MailChimp\Core\Action\IMailChimpAction;
use XLite\Module\XC\MailChimp\Core\MailChimpECommerce;
use XLite\Module\XC\MailChimp\Main;

class CouponUpdate implements IMailChimpAction
{
    /**
     * @var \XLite\Module\CDev\Coupons\Model\Coupon
     */
    private $coupon;

    /**
     * @inheritDoc
     */
    public function __construct(\XLite\Module\CDev\Coupons\Model\Coupon $coupon)
    {
        $this->coupon = $coupon;
    }

    /**
     *
     */
    public function execute(): void
    {
        $ecCore = MailChimpECommerce::getInstance();

        foreach (Main::getMainStores() as $store) {
            $updateResult = $ecCore->updateCoupon($store->getId(), $this->coupon);
            if (!$updateResult) {
                $ecCore->createCoupon($store->getId(), $this->coupon);
            }
        }
    }
}