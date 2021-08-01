<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Sale\Controller\Admin;

/**
 * Sale
 */
class SaleDiscount extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Controller parameters
     *
     * @var   array
     */
    protected $params = array('target', 'id', 'page');

    /**
     * Check ACL permissions
     *
     * @return boolean
     */
    public function checkACL()
    {
        return parent::checkACL()
            || \XLite\Core\Auth::getInstance()->isPermissionAllowed('manage sale discounts');
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
            ? $model->getName()
            : static::t('New sale');
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

        if ($this->getSaleDiscount()
            && $this->getSaleDiscount()->isPersistent()
            && $this->getSaleDiscount()->getSpecificProducts()
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
        $list['info']    = 'modules/CDev/Sale/sale_discount/info.twig';
        $list['default'] = 'modules/CDev/Sale/sale_discount/info.twig';

        if ($this->getSaleDiscount()
            && $this->getSaleDiscount()->isPersistent()
            && $this->getSaleDiscount()->getSpecificProducts()
        ) {
            $list['products'] = 'modules/CDev/Sale/sale_discount/products.twig';
        }

        return $list;
    }

    /**
     * Update sale discount
     *
     * @return void
     */
    public function doActionUpdate()
    {
        $this->getModelForm()->performAction('modify');

        if ($this->getModelForm()->isValid()) {
            if ($this->getSaleDiscount()->getSpecificProducts()) {
                $this->setReturnURL(
                    $this->buildURL(
                        'sale_discount',
                        '',
                        ['id' => $this->getSaleDiscountId()]
                    )
                );
            } else {
                $this->setReturnURL(
                    \XLite\Core\Converter::buildURL(
                        'promotions',
                        '',
                        ['page' => \XLite\Controller\Admin\Promotions::PAGE_SALE_DISCOUNTS]
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

            $assignedProductIds = $this->getSaleDiscount()->getApplicableProductIds();

            $count = 0;
            if ($products) {
                foreach ($products as $product) {
                    /** @var \XLite\Model\Product $product */
                    if (!in_array($product->getProductId(), $assignedProductIds)) {
                        $saleDiscountProduct = new \XLite\Module\CDev\Sale\Model\SaleDiscountProduct();
                        $saleDiscountProduct->setProduct($product);
                        $saleDiscountProduct->setSaleDiscount($this->getSaleDiscount());

                        $count++;
                        \XLite\Core\Database::getEM()->persist($saleDiscountProduct);
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
                'sale_discount',
                '',
                array(
                    'page' => 'products',
                    'id'   => $this->getSaleDiscountId(),
                )
            )
        );
        $this->setHardRedirect(true);
    }

    /**
     * @return int|null
     */
    public function getSaleDiscountId()
    {
        return $this->getSaleDiscount() ? $this->getSaleDiscount()->getId() : null;
    }

    /**
     * Returns sale discount
     *
     * @return \XLite\Module\CDev\Sale\Model\SaleDiscount
     */
    protected function getSaleDiscount()
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
        return 'XLite\Module\CDev\Sale\View\Model\SaleDiscount';
    }

}
