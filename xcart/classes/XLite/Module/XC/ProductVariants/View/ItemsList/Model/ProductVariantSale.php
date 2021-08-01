<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductVariants\View\ItemsList\Model;

/**
 * Product variants items list
 *
 * @Decorator\Depend("CDev\Sale")
 */
class ProductVariantSale extends \XLite\Module\XC\ProductVariants\View\ItemsList\Model\ProductVariant implements \XLite\Base\IDecorator
{
    /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
        $columns = parent::defineColumns();

        if (\XLite\Model\Product::SALE_DISCOUNT_TYPE_PRICE === $this->getProduct()->getDiscountType()) {
            $defaultSale = $this->formatPrice($this->getProduct()->getSalePriceValue());
        } else {
            $defaultSale = $this->getProduct()->getSalePriceValue() . '%';
        }

        if (!$this->getProduct()->getParticipateSale()) {
            $defaultSale = '0%';
        }

        $columns['sale'] = [
            static::COLUMN_NAME      => static::t('Sale'),
            static::COLUMN_SUBHEADER => static::t('Default') . ': ' . $defaultSale,
            static::COLUMN_CLASS     => 'XLite\Module\XC\ProductVariants\View\FormField\Inline\Input\Sale',
            static::COLUMN_EDIT_ONLY => true,
            static::COLUMN_ORDERBY   => 450,
        ];

        return $columns;
    }

    /**
     * Pre-validate entity
     *
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return boolean
     */
    protected function prevalidateEntity(\XLite\Model\AEntity $entity)
    {
        $result = parent::prevalidateEntity($entity);

        return $result && $this->prevalidateSaleDiscount($entity);
    }

    /**
     * Pre-validate entity
     *
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return boolean
     */
    protected function prevalidateSaleDiscount(\XLite\Model\AEntity $entity)
    {
        $result = true;

        if (\XLite\Model\Product::SALE_DISCOUNT_TYPE_PERCENT == $entity->getDiscountType()) {
            if (100 < $entity->getSalePriceValue()) {
                $this->errorMessages[] = static::t('Percent discount value cannot exceed 100%');
                $result = false;
            }
        }

        return $result;
    }

}
