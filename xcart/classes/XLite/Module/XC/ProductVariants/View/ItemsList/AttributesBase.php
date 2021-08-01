<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductVariants\View\ItemsList;


class AttributesBase extends \XLite\View\ItemsList\Model\Table
{
    public function getCSSFiles()
    {
        return array_merge(parent::getCSSFiles(), [
            'modules/XC/ProductVariants/items_list/model/product_variant/style.less'
        ]);
    }

    /**
     * @inheritdoc
     */
    protected function defineColumns()
    {
        return [
            'name'    => [
                static::COLUMN_NO_WRAP => true,
                static::COLUMN_NAME    => static::t('Attribute'),
                static::COLUMN_PARAMS  => ['required' => true],
                static::COLUMN_ORDERBY => 100,
            ],
            'options' => [
                static::COLUMN_MAIN    => true,
                static::COLUMN_NAME    => static::t('Options'),
                static::COLUMN_TEMPLATE => 'modules/XC/ProductVariants/variants/parts/variants_are_based/parts/options.twig',
                static::COLUMN_ORDERBY => 200,
            ],
        ];
    }

    /**
     * Get options
     *
     * @param $entity
     *
     * @return string
     */
    protected function getOptions($entity)
    {
        return $entity->getAttributeValue($this->getProduct(), true);
    }

    /**
     * @inheritdoc
     */
    protected function getData(\XLite\Core\CommonCell $cnd, $countOnly = false)
    {
        return $countOnly
            ? count($this->getMultipleAttributes())
            : $this->getMultipleAttributes();
    }

    /**
     * @inheritdoc
     */
    protected function defineRepositoryName()
    {
        return '\XLite\Model\Attribute';
    }

    /**
     * @inheritdoc
     */
    protected function isSelectable()
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    protected function getPanelClass()
    {
        return '';
    }

    /**
     * Check if row selected
     *
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return bool
     */
    protected function isRowSelected($entity)
    {
        return in_array($entity->getId(), $this->getVariantsAttributeIds());
    }
}