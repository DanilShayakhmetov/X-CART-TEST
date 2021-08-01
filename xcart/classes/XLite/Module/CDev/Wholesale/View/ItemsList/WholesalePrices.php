<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Wholesale\View\ItemsList;

/**
 * Wholesale prices items list
 */
class WholesalePrices extends \XLite\View\ItemsList\Model\Table
{
    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/CDev/Wholesale/pricing/style.less';

        return $list;
    }

    /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
        return [
            'quantityRangeBegin' => [
                static::COLUMN_NAME    => \XLite\Core\Translation::lbl('Quantity range'),
                static::COLUMN_CLASS   => 'XLite\Module\CDev\Wholesale\View\FormField\QuantityRangeBegin',
                static::COLUMN_ORDERBY => 100,
            ],
            'membership'         => [
                static::COLUMN_NAME    => \XLite\Core\Translation::lbl('Membership'),
                static::COLUMN_CLASS   => 'XLite\Module\CDev\Wholesale\View\FormField\Membership',
                static::COLUMN_ORDERBY => 200,
            ],
            'price'              => [
                static::COLUMN_CLASS   => '\XLite\Module\CDev\Wholesale\View\FormField\Inline\Input\WholesalePriceOrPercent',
                static::COLUMN_ORDERBY => 300,
            ],
            'resultPrice'              => [
                static::COLUMN_NAME      => \XLite\Core\Translation::lbl('Price'),
                static::COLUMN_SUBHEADER => static::t('Basic price') . ': ' . $this->formatPrice($this->getDefaultPriceValue()),
                static::COLUMN_ORDERBY   => 400,
            ],
            'save'              => [
                static::COLUMN_NAME    => \XLite\Core\Translation::lbl('Saving'),
                static::COLUMN_ORDERBY => 500,
            ],
        ];
    }

    /**
     * @return array
     */
    protected function getColspanHeaders()
    {
        return ['resultPrice' => ['price']];
    }

    /**
     * @param $entity \XLite\Module\CDev\Wholesale\Model\WholesalePrice
     * @return string
     */
    protected function getResultPriceColumnValue($entity)
    {
        return static::formatPriceHTML($entity->getClearPrice());
    }

    /**
     * @param $entity \XLite\Module\CDev\Wholesale\Model\WholesalePrice
     * @return string
     */
    protected function getSaveColumnValue($entity)
    {
        if ($entity->getOwner() && $entity->getOwnerPrice()) {
            return static::formatPriceHTML($entity->getOwnerPrice() - $entity->getClearPrice())
                . " (" . round(100 - ($entity->getClearPrice() / $entity->getOwnerPrice() * 100), 2) . "%)";
        }

        return '';
    }

    /**
     * Define repository name
     *
     * @return string
     */
    protected function defineRepositoryName()
    {
        return 'XLite\Module\CDev\Wholesale\Model\WholesalePrice';
    }

    /**
     * Return class name for the list pager
     *
     * @return string
     */
    protected function getPagerClass()
    {
        return 'XLite\View\Pager\Admin\Model\Infinity';
    }

    /**
     * Get create button label
     *
     * @return string
     */
    protected function getCreateButtonLabel()
    {
        return 'New tier';
    }

    /**
     * Mark list as switchable (enable / disable)
     *
     * @return boolean
     */
    protected function isDisplayWithEmptyList()
    {
        return true;
    }

    /**
     * Mark list as switchable (enable / disable)
     *
     * @return boolean
     */
    protected function isSwitchable()
    {
        return false;
    }

    /**
     * Mark list as removable
     *
     * @return boolean
     */
    protected function isRemoved()
    {
        return true;
    }

    /**
     * Inline creation mechanism position
     *
     * @return integer
     */
    protected function isInlineCreation()
    {
        return static::CREATE_INLINE_TOP;
    }

    /**
     * isEmptyListTemplateVisible
     *
     * @return boolean
     */
    protected function isEmptyListTemplateVisible()
    {
        return false;
    }

    /**
     * Get list name suffixes
     *
     * @return array
     */
    protected function getListNameSuffixes()
    {
        return ['wholesalePrices'];
    }

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' wholesale-prices';
    }

    /**
     * @inheritdoc
     */
    protected function createEntity()
    {
        $entity = parent::createEntity();

        $entity->setProduct($this->getProduct());

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
        // Search wholesale prices to display in the items list
        $cnd->{\XLite\Module\CDev\Wholesale\Model\Repo\WholesalePrice::P_PRODUCT} = $this->getProduct();
        $cnd->{\XLite\Module\CDev\Wholesale\Model\Repo\WholesalePrice::P_ORDER_BY} = [
            ['w.membership', 'ASC'],
            ['w.quantityRangeBegin', 'ASC'],
        ];

        return \XLite\Core\Database::getRepo('XLite\Module\CDev\Wholesale\Model\WholesalePrice')
            ->search($cnd, $countOnly);
    }

    /**
     * Return default price
     *
     * @return mixed
     */
    protected function getDefaultPriceValue()
    {
        return $this->getProduct()->getBasePrice();
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
        $this->commonParams['product_id'] = \XLite\Core\Request::getInstance()->product_id;
        $this->commonParams['page'] = 'wholesale_pricing';

        return $this->commonParams;
    }

    /**
     * @inheritdoc
     */
    protected function prevalidateEntities()
    {
        if (parent::prevalidateEntities()) {
            $entities = $this->getPageDataForUpdate();

            /** @var \XLite\Module\CDev\Wholesale\Model\Base\AWholesalePrice $entity */
            foreach ($this->getPageDataForUpdate() as $entity) {
                if (!$entity->getMembership() && $entity->getQuantityRangeBegin() === 1) {
                    $this->errorMessages[] = static::t('The base price can not be changed on this page.');
                    return false;
                }

                if ($tier = $this->getTierByWholesaleEntity($entity)) {
                    if ($tier->getId() !== $entity->getId()) {
                        $this->errorMessages[] = static::t('Tier with same quantity range and membership already defined.');
                        return false;
                    }
                }

                if (array_filter($entities, function ($tier) use ($entity) {
                    return $entity->getMembership() === $tier->getMembership()
                           && $entity->getQuantityRangeBegin() === $tier->getQuantityRangeBegin()
                           && $entity->getId() !== $tier->getId();
                })) {
                    $this->errorMessages[] = static::t('Tier with same quantity range and membership already defined.');
                    return false;
                }
            }

            return true;
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    protected function prevalidateNewEntity(\XLite\Model\AEntity $entity)
    {
        /** @var \XLite\Module\CDev\Wholesale\Model\Base\AWholesalePrice $entity */
        $result = parent::prevalidateNewEntity($entity);

        if ($result && !$entity->getMembership() && $entity->getQuantityRangeBegin() === 1) {
            $this->errorMessages[] = static::t('The base price can not be changed on this page.');
            return false;
        }

        if ($result && $this->getTierByWholesaleEntity($entity)) {
            $this->errorMessages[] = static::t('Tier with same quantity range and membership already defined.');
            return false;
        }

        return $result;
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
            'product'            => $this->getProduct(),
        ]);
    }
}
