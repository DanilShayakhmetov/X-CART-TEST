<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XPay\XPaymentsCloud\Module\QSL\XPaymentsSubscriptions\Controller\Admin;

use XLite\Module\XPay\XPaymentsCloud\Main as XPaymentsCloud;

/**
 * Class Product
 * @Decorator\Depend({"XPay\XPaymentsCloud", "QSL\XPaymentsSubscriptions"})
 */
class Product extends \XLite\Controller\Admin\Product implements \XLite\Base\IDecorator
{
    /**
     * Remove page with legacy subscription plan since it's a duplicate of XPay/XPaymentsCloud subscription plan
     *
     * @return array
     */
    public function getPages()
    {
        $list = parent::getPages();

        if (
            isset($list[static::PAGE_SUBSCRIPTION_PLAN])
            && XPaymentsCloud::isUseXpaymentsCloudForSubscriptions()
        ) {
            unset($list[static::PAGE_SUBSCRIPTION_PLAN]);
        }

        if (
            isset($list[static::PAGE_XPAYMENTS_SUBSCRIPTION_PLAN])
            && !XPaymentsCloud::isUseXpaymentsCloudForSubscriptions()
        ) {
            unset($list[static::PAGE_XPAYMENTS_SUBSCRIPTION_PLAN]);
        }

        return $list;
    }

    /**
     * @inheritDoc
     */
    public function doActionSaveSubscriptionPlan()
    {
        parent::doActionSaveSubscriptionPlan();

        $product = $this->getProduct();
        $data = \XLite\Core\Request::getInstance()->getPostData();
        if ($product && $data) {
            $xpaymentsCloudPlanRepo = \XLite\Core\Database::getRepo('\XLite\Module\XPay\XPaymentsCloud\Model\Subscription\Plan');
            /** @var \XLite\Module\QSL\XPaymentsSubscriptions\Model\SubscriptionPlan $xpaymentsCloudPlan */
            $xpaymentsCloudPlan = $xpaymentsCloudPlanRepo->findOneBy(['product' => $product]);
            if (!$xpaymentsCloudPlan) {
                $xpaymentsCloudPlan = new \XLite\Module\XPay\XPaymentsCloud\Model\Subscription\Plan();
                \XLite\Core\Database::getEM()->persist($xpaymentsCloudPlan);
            }
            $xpaymentsCloudPlan->updateByLegacyPlanData($data, $product);
            \XLite\Core\Database::getEM()->flush();
        }
    }

    /**
     * @inheritDoc
     */
    public function doActionSaveXpaymentsSubscriptionPlan()
    {
        parent::doActionSaveXpaymentsSubscriptionPlan();

        $product = $this->getProduct();
        $data = \XLite\Core\Request::getInstance()->getPostData();
        if ($product && $data) {
            $legacyPlanRepo = \XLite\Core\Database::getRepo('\XLite\Module\QSL\XPaymentsSubscriptions\Model\SubscriptionPlan');
            /** @var \XLite\Module\QSL\XPaymentsSubscriptions\Model\SubscriptionPlan $legacyPlan */
            $legacyPlan = $legacyPlanRepo->findOneBy(['product' => $product]);
            if (!$legacyPlan) {
                $legacyPlan = new \XLite\Module\QSL\XPaymentsSubscriptions\Model\SubscriptionPlan();
                \XLite\Core\Database::getEM()->persist($legacyPlan);
            }
            $legacyPlan->updateByXpaymentsCloudData($data, $product);
            \XLite\Core\Database::getEM()->flush();
        }
    }

}
