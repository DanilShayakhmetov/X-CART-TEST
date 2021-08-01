<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Coupons\Controller\Admin;

/**
 * Coupon
 */
class Coupon extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Controller parameters
     *
     * @var   array
     */
    protected $params = array('target', 'id', 'page');

    /**
     * Coupon id
     *
     * @var   integer
     */
    protected $id;

    /**
     * Check ACL permissions
     *
     * @return boolean
     */
    public function checkACL()
    {
        return parent::checkACL()
            || \XLite\Core\Auth::getInstance()->isPermissionAllowed('manage coupons');
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        $model = $this->getModelForm()->getModelObject();

        return ($model && $model->getId())
            ? $model->getCode()
            : static::t('Coupon');
    }

    /**
     * Get pages sections
     *
     * @return array
     */
    public function getPages()
    {
        $list = parent::getPages();
        $list['info'] = static::t('Info');

        if ($this->getCoupon()
            && $this->getCoupon()->isPersistent()
            && $this->getCoupon()->getSpecificProducts()
        ) {
            $list['products']  = static::t('Products');
        }

        return $list;
    }

    /**
     * Get pages templates
     *
     * @return array
     */
    protected function getPageTemplates()
    {
        $list = parent::getPageTemplates();
        $list['info']    = 'modules/CDev/Coupons/coupon/info.twig';
        $list['default'] = 'modules/CDev/Coupons/coupon/info.twig';

        if ($this->getCoupon()
            && $this->getCoupon()->isPersistent()
            && $this->getCoupon()->getSpecificProducts()
        ) {
            $list['products'] = 'modules/CDev/Coupons/coupon/products.twig';
        }

        return $list;
    }

    /**
     * Update coupon
     *
     * @return void
     */
    public function doActionUpdate()
    {
        $this->getModelForm()->performAction('modify');

        if ($this->getModelForm()->isValid()) {
            if ($this->getCoupon()->getSpecificProducts()) {
                $this->setReturnURL(
                    $this->buildURL(
                        'coupon',
                        '',
                        ['id' => $this->getCouponId()]
                    )
                );
            } else {
                $this->setReturnURL(
                    \XLite\Core\Converter::buildURL(
                        'promotions',
                        '',
                        ['page' => \XLite\Controller\Admin\Promotions::PAGE_COUPONS]
                    )
                );
            }
        }
    }

    public function doActionAddProducts()
    {
        $productIds = \XLite\Core\Request::getInstance()->select;

        if (is_array($productIds)) {
            $products = \XLite\Core\Database::getRepo('\XLite\Model\Product')
                ->findByIds($productIds);

            $assignedProductIds = $this->getCoupon()->getApplicableProductIds();

            $count = 0;
            if ($products) {
                foreach ($products as $product) {
                    /** @var \XLite\Model\Product $product */
                    if (!in_array($product->getProductId(), $assignedProductIds)) {
                        $couponProduct = new \XLite\Module\CDev\Coupons\Model\CouponProduct();
                        $couponProduct->setProduct($product);
                        $couponProduct->setCoupon($this->getCoupon());

                        $count++;
                        \XLite\Core\Database::getEM()->persist($couponProduct);
                    }
                }
            }

            if ($count > 0) {
                \XLite\Core\TopMessage::addInfo('X product(s) added', ['count' => $count]);
            }

            \XLite\Core\Database::getEM()->flush();
        }

        $this->setReturnURL(
            $this->buildURL(
                'coupon',
                '',
                array(
                    'page' => 'products',
                    'id'   => $this->getCouponId(),
                )
            )
        );
        $this->setHardRedirect(true);
    }

    /**
     * @return int|null
     */
    public function getCouponId()
    {
        return $this->getCoupon() ? $this->getCoupon()->getId() : null;
    }

    /**
     * Returns coupon
     *
     * @return \XLite\Module\CDev\Coupons\Model\Coupon
     */
    protected function getCoupon()
    {
        return $this->getModelForm()->getModelObject();
    }

    /**
     * Get model form class
     *
     * @return string
     */
    protected function getModelFormClass()
    {
        return 'XLite\Module\CDev\Coupons\View\Model\Coupon';
    }
}
