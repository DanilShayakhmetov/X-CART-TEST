<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XPay\XPaymentsCloud\View\ItemsList\Model\Order\Admin;

/**
 * Search order
 */
class Search extends \XLite\View\ItemsList\Model\Order\Admin\Search implements \XLite\Base\IDecorator
{
    const SORT_BY_MODE_FRAUD =  'p.fraud';
    /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
        $columns = parent::defineColumns();

        $columns['fraud-status-xpayments'] = [
            static::COLUMN_NAME     => '',
            static::COLUMN_SORT     => static::SORT_BY_MODE_FRAUD,
            static::COLUMN_LINK     => 'order',
            static::COLUMN_TEMPLATE => 'modules/XPay/XPaymentsCloud/order/fraud_status/status.twig',
            static::COLUMN_ORDERBY  => 350,
        ];

        return $columns;
    }

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/XPay/XPaymentsCloud/order/style.css';

        return $list;
    }

    /**
     * Get column value
     *
     * @param array                $column Column
     * @param \XLite\Model\AEntity $entity Model
     *
     * @return mixed
     */
    protected function getColumnClass(array $column, \XLite\Model\AEntity $entity = null)
    {
        $result = parent::getColumnClass($column, $entity);

        if ('fraud-status-xpayments' == $column[static::COLUMN_CODE]) {
            $result = 'fraud-status-' . $entity->getXpaymentsFraudStatus();
        }

        return $result;
    }

    /**
     * Build entity page URL
     *
     * @param \XLite\Model\AEntity $entity Entity
     * @param array                $column Column data
     *
     * @return string
     */
    protected function getXpaymentsFraudInfoLink(\XLite\Model\AEntity $entity)
    {
        $result = \XLite\Core\Converter::buildURL(
            'order',
            '',
            array('order_number' => $entity->getOrderNumber())
        );

        $result .= '#' . $entity->getXpaymentsFraudInfoAnchor();

        return $result;
    }

    /**
     * Get column value
     *
     * @param array                $column Column
     * @param \XLite\Model\AEntity $entity Model
     *
     * @return mixed
     */
    protected function getXpaymentsFraudInfoTitle(\XLite\Model\AEntity $entity)
    {
        return $entity->getXpaymentsFraudStatus();
    }
}
