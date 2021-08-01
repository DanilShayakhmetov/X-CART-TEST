<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Wholesale\View\ItemsList;

/**
 * Wholesale prices items list (product variant)
 */
class ProductVariantWholesalePrices extends \XLite\Module\CDev\Wholesale\View\ItemsList\WholesalePrices
{
    /**
     * Define repository name
     *
     * @return string
     */
    protected function defineRepositoryName()
    {
        return 'XLite\Module\CDev\Wholesale\Model\ProductVariantWholesalePrice';
    }

    /**
     * createEntity
     *
     * @return \XLite\Module\XC\ProductVariants\Model\ProductVariant
     */
    protected function createEntity()
    {
        $entity = parent::createEntity();

        $entity->setProductVariant($this->getProductVariant());

        return $entity;
    }

    // {{{ Data

    /**
     * Return wholesale prices
     *
     * @param \XLite\Core\CommonCell $cnd       Search condition
     * @param boolean                $countOnly Return items list or only its size OPTIONAL
     *
     * @return array|integer
     */
    protected function getData(\XLite\Core\CommonCell $cnd, $countOnly = false)
    {
        $cnd->{\XLite\Module\CDev\Wholesale\Model\Repo\ProductVariantWholesalePrice::P_PRODUCT_VARIANT} = $this->getProductVariant();
        $cnd->{\XLite\Module\CDev\Wholesale\Model\Repo\ProductVariantWholesalePrice::P_ORDER_BY_MEMBERSHIP} = true;
        $cnd->{\XLite\Module\CDev\Wholesale\Model\Repo\ProductVariantWholesalePrice::P_ORDER_BY} = ['w.quantityRangeBegin', 'ASC'];

        $result = \XLite\Core\Database::getRepo('XLite\Module\CDev\Wholesale\Model\ProductVariantWholesalePrice')
            ->search($cnd, $countOnly);

        return $result;
    }

    /**
     * Return default price
     *
     * @return mixed
     */
    protected function getDefaultPriceValue()
    {
        return $this->getProductVariant()->getClearPrice();
    }

    // }}}

    /**
     * Get URL common parameters
     *
     * @return array
     */
    protected function getCommonParams()
    {
        $this->commonParams = parent::getCommonParams();
        $this->commonParams['id'] = \XLite\Core\Request::getInstance()->id;

        return $this->commonParams;
    }

    /**
     * Get tier by quantity and membership
     *
     * @param \XLite\Module\CDev\Wholesale\Model\Base\AWholesalePrice $entity
     *
     * @return \XLite\Module\CDev\Wholesale\Model\Base\AWholesalePrice
     */
    protected function getTierByWholesaleEntity($entity)
    {
        return $entity->getRepository()->findOneBy([
            'quantityRangeBegin' => $entity->getQuantityRangeBegin(),
            'membership'         => $entity->getMembership(),
            'productVariant'     => $this->getProductVariant(),
        ]);
    }
}
