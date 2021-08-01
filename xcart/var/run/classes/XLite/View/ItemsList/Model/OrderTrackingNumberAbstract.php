<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ItemsList\Model;

/**
 * Order tracking number items list
 *
 *  ListChild (list="order.actions", weight="9999", zone="admin")
 */
abstract class OrderTrackingNumberAbstract extends \XLite\View\ItemsList\Model\Table
{
    /**
     * Widget parameter name
     */
    const PARAM_ORDER_ID = 'orderId';

    /**
     * Defines the CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'order/page/tracking.less';

        return $list;
    }

    /**
     * Defines the CSS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'order/page/tracking.js';

        return $list;
    }

    /**
     * Get data prefix
     *
     * @return string
     */
    public function getDataPrefix()
    {
        return 'tracking';
    }

    /**
     * Get data prefix for new data
     *
     * @return string
     */
    public function getCreateDataPrefix()
    {
        return 'new-tracking';
    }

    protected function postprocessInsertedEntity(\XLite\Model\AEntity $entity, array $line)
    {
        $this->getOrder()->addTrackingNumbers($entity);

        return parent::postprocessInsertedEntity($entity, $line);
    }

    /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
        $columns = array(
            'value' => array(
                static::COLUMN_CLASS    => 'XLite\View\FormField\Inline\Input\Text',
                static::COLUMN_PARAMS   => array('required' => true, 'placeholder' => static::t('Enter tracking number')),
                static::COLUMN_MAIN     => true,
                static::COLUMN_NAME     => static::t('Tracking number'),
                static::COLUMN_ORDERBY  => 200,
            ),
            'creationDate' => array(
                static::COLUMN_NAME     => static::t('Creation date'),
                static::COLUMN_TEMPLATE => $this->getDir() . '/' . $this->getPageBodyDir() . '/order_tracking_number/cell.creation_date.twig',
                static::COLUMN_ORDERBY  => 100,
            )
        );

        if ($this->getOrder()->getTrackingInformationURL('')) {
            $columns['track'] = array(
                static::COLUMN_NAME     => static::t('Payment status'),
                static::COLUMN_LINK     => 'track',
                static::COLUMN_TEMPLATE => $this->getDir() . '/' . $this->getPageBodyDir() . '/order_tracking_number/cell.track.twig',
                static::COLUMN_ORDERBY  => 300,
            );
        }

        return $columns;
    }

    /**
     * Define repository name
     *
     * @return string
     */
    protected function defineRepositoryName()
    {
        return 'XLite\Model\OrderTrackingNumber';
    }

    /**
     * Get create entity URL
     *
     * @return string
     */
    protected function getCreateURL()
    {
        return $this->buildURL('order');
    }

    /**
     * Get create button label
     *
     * @return string
     */
    protected function getCreateButtonLabel()
    {
        return 'Add tracking number';
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


    // {{{ Behaviors

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

    // }}}

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' tracking-number';
    }

    /**
     * Check - sticky panel is visible or not
     *
     * @return boolean
     */
    protected function isPanelVisible()
    {
        return false;
    }

    // {{{ Search

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            static::PARAM_ORDER_ID => new \XLite\Model\WidgetParam\TypeInt(
                'OrderID ', null
            ),
        );
    }

    /**
     * Get pager class name
     *
     * @return string
     */
    protected function getPagerClass()
    {
        return 'XLite\View\Pager\Admin\Model\Infinity';
    }

    // {{{ Search

    /**
     * Return search parameters.
     *
     * @return array
     */
    public static function getSearchParams()
    {
        return array(
            \XLite\Model\Repo\OrderTrackingNumber::P_ORDER_ID => static::PARAM_ORDER_ID,
        );
    }

    /**
     * Get search values storage
     *
     * @param boolean $forceFallback Force fallback to session storage
     *
     * @return \XLite\View\ItemsList\ISearchValuesStorage
     */
    public static function getSearchValuesStorage($forceFallback = false)
    {
        $storage = parent::getSearchValuesStorage($forceFallback);

        $orderId = (\XLite::getController()->getOrder() ? \XLite::getController()->getOrder()->getOrderId() : null);

        $storage->setValue(\XLite\Model\Repo\OrderTrackingNumber::P_ORDER_ID, $orderId);

        return $storage;
    }

    /**
     * Create entity
     *
     * @return \XLite\Model\AEntity
     */
    protected function createEntity()
    {
        $entity = parent::createEntity();

        $entity->setOrder($this->getOrder());

        return $entity;
    }

    /*
     * getEmptyListTemplate
     *
     * @return string
     */
    protected function getEmptyListTemplate()
    {
        return 'order/page/no_tracking_numbers.twig';
    }

    /**
     * Check - table header is visible or not
     *
     * @return boolean
     */
    protected function isTableHeaderVisible()
    {
        return false;
    }

    /**
     * Get top actions
     *
     * @return array
     */
    protected function getTopActions()
    {
        $actions = parent::getTopActions();

        // "Send tracking information" button is visible if the tracking numbers are provided
        if ($this->hasResults() && !$this->isStatic()) {
            $actions[] = 'order/page/parts/send_tracking.twig';
        }

        return $actions;
    }

    // }}}
}
