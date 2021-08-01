<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MultiCurrency\View\ItemsList\Model\Currency;

/**
 * Active currencies list
 */
class ActiveCurrencies extends \XLite\View\ItemsList\Model\Table
{
    /**
     * Define repository name
     *
     * @return string
     */
    protected function defineRepositoryName()
    {
        return 'XLite\Module\XC\MultiCurrency\Model\ActiveCurrency';
    }

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'modules/XC/MultiCurrency/controller.js';

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
            'name'          => [
                static::COLUMN_NAME    => \XLite\Core\Translation::lbl('Name'),
                static::COLUMN_CLASS   => '\XLite\View\FormField\Inline\Label',
                static::COLUMN_NO_WRAP => true,
                static::COLUMN_ORDERBY => 100,
            ],
            'code'          => [
                static::COLUMN_NAME    => \XLite\Core\Translation::lbl('Code'),
                static::COLUMN_CLASS   => '\XLite\View\FormField\Inline\Label',
                static::COLUMN_NO_WRAP => true,
                static::COLUMN_ORDERBY => 200,
            ],
            'format'        => [
                static::COLUMN_NAME    => \XLite\Core\Translation::lbl('Format'),
                static::COLUMN_CLASS   => '\XLite\View\FormField\Inline\Select\FloatFormat',
                static::COLUMN_ORDERBY => 300,
            ],
            'prefix'        => [
                static::COLUMN_NAME    => \XLite\Core\Translation::lbl('Prefix'),
                static::COLUMN_CLASS   => '\XLite\View\FormField\Inline\Input\Text',
                static::COLUMN_NO_WRAP => true,
                static::COLUMN_ORDERBY => 400,
                static::COLUMN_PARAMS => [
                    \XLite\View\FormField\Input\Base\StringInput::PARAM_MAX_LENGTH => \XLite\Core\Database::getRepo('XLite\Model\Currency')->getFieldInfo('prefix', 'length'),
                ],
            ],
            'suffix'        => [
                static::COLUMN_NAME    => \XLite\Core\Translation::lbl('Suffix'),
                static::COLUMN_CLASS   => '\XLite\View\FormField\Inline\Input\Text',
                static::COLUMN_NO_WRAP => true,
                static::COLUMN_ORDERBY => 500,
                static::COLUMN_PARAMS => [
                    \XLite\View\FormField\Input\Base\StringInput::PARAM_MAX_LENGTH => \XLite\Core\Database::getRepo('XLite\Model\Currency')->getFieldInfo('suffix', 'length'),
                ],
            ],
            'rate'          => [
                static::COLUMN_NAME    => \XLite\Core\Translation::lbl('Rate'),
                static::COLUMN_CLASS   => '\XLite\View\FormField\Inline\Input\Text\FloatInput',
                static::COLUMN_PARAMS  => [
                    \XLite\View\FormField\Input\Text\FloatInput::PARAM_E => 4,
                ],
                static::COLUMN_NO_WRAP => true,
                static::COLUMN_ORDERBY => 600,
            ],
//            'roundUp' => [
//                static::COLUMN_NAME    => \XLite\Core\Translation::lbl('RoundUp'),
//                static::COLUMN_CLASS   => '\XLite\View\FormField\Inline\Select\RoundUp',
//                static::COLUMN_ORDERBY => 700,
//            ],
            'countries' => [
                static::COLUMN_NAME    => \XLite\Core\Translation::lbl('Countries'),
                static::COLUMN_MAIN    => true,
                static::COLUMN_CLASS   => '\XLite\View\FormField\Inline\Select\Select2\Countries',
                static::COLUMN_ORDERBY => 800,
            ],
        ];
    }

    /**
     * Mark list as removable
     *
     * @return boolean
     */
    protected function isRemoved()
    {
        return $this->getItemsCount() > 1;
    }

    /**
     * Mark list item as default
     *
     * @return boolean
     */
    protected function isDefault()
    {
        return true;
    }

    /**
     * @param \XLite\Module\XC\MultiCurrency\Model\ActiveCurrency $entity
     *
     * @inheritdoc
     */
    protected function isAllowEntityRemove(\XLite\Model\AEntity $entity)
    {
        return parent::isAllowEntityRemove($entity) && !$entity->isDefaultCurrency();
    }

    /**
     * @param \XLite\Module\XC\MultiCurrency\Model\ActiveCurrency $entity
     *
     * @inheritdoc
     */
    protected function removeEntity(\XLite\Model\AEntity $entity)
    {
        if (parent::removeEntity($entity)) {
            if ($entity->getCurrency()) {
                $entity->getCurrency()->setActiveCurrency(null);
            }

            /* @var \XLite\Model\Country $country */
            foreach ($entity->getCountries() as $country) {
                $country->setActiveCurrency(null);
            }

            return true;
        }

        return false;
    }

    /**
     * @param \XLite\Module\XC\MultiCurrency\Model\ActiveCurrency $entity
     *
     * @inheritdoc
     */
    protected function setDefaultValue($entity, $value)
    {
        if ($value) {
            if (!$entity->isDefaultCurrency()) {
                \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption([
                    'category' => 'General',
                    'name'     => 'shop_currency',
                    'value'    => $entity->getCurrency()->getCurrencyId()
                ]);

                $entity->setEnabled(1);
                $entity->setRate(1);
                $entity->setRateDate(0);
            }
        }
    }

    /**
     * Mark list as switchable (enable / disable)
     *
     * @return boolean
     */
    protected function isSwitchable()
    {
        return true;
    }

    /**
     * Defines the position MOVE widget class name
     *
     * @return string
     */
    protected function getMovePositionWidgetClassName()
    {
        return 'XLite\View\FormField\Inline\Input\Text\Position\Move';
    }

    /**
     * Defines the position OrderBy widget class name
     *
     * @return string
     */
    protected function getOrderByWidgetClassName()
    {
        return 'XLite\View\FormField\Inline\Input\Text\Position\OrderBy';
    }

    /**
     * Mark list as sortable
     *
     * @return integer
     */
    protected function getSortableType()
    {
        return static::SORT_TYPE_MOVE;
    }

    /**
     * Return params list to use for search
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchCondition()
    {
        $cnd = parent::getSearchCondition();

        $cnd->{\XLite\Module\XC\MultiCurrency\Model\Repo\ActiveCurrency::AC_ORDER_BY_POSITION} = 'ASC';

        return $cnd;
    }

    /**
     * @inheritdoc
     */
    protected function getPanelClass()
    {
        return 'XLite\Module\XC\MultiCurrency\View\StickyPanel\ActiveCurrencies';
    }

    /**
     * Returns list of available country codes
     *
     * @return array
     */
    protected function getUnavailableCountries()
    {
        $qb = \XLite\Core\Database::getRepo('XLite\Model\Country')->createQueryBuilder();
        $alias = $qb->getMainAlias();
        $qb->select("{$alias}.code")
            ->andWhere("{$alias}.active_currency IS NOT NULL")
            ->groupBy("{$alias}.code");

        $list = $qb->getResult();

        return array_filter(array_map(function ($row) {
            return isset($row['code'])
                ? $row['code']
                : null;
        }, $list));
    }

    /**
     * @inheritdoc
     */
    public function displayCommentedData(array $data)
    {
        $data['unavailableCountries'] = $this->getUnavailableCountries();

        parent::displayCommentedData($data);
    }
}
