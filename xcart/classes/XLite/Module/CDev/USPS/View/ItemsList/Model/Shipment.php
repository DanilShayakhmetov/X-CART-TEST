<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\USPS\View\ItemsList\Model;

class Shipment extends \XLite\View\ItemsList\Model\Table
{
    /**
     * Search parameter name
     */
    const PARAM_ORDER_ID = 'order_id';

    /**
     * Return search parameters
     *
     * @return array
     */
    public static function getSearchParams()
    {
        return [
            \XLite\Module\CDev\USPS\Model\Repo\Shipment::SEARCH_ORDER_ID => static::PARAM_ORDER_ID,
        ];
    }

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list   = parent::getCSSFiles();
        $list[] = 'modules/CDev/USPS/shipments/shipments.css';

        return $list;
    }

    /**
     * Get wrapper form target
     *
     * @return string
     */
    protected function getFormTarget()
    {
        return 'order';
    }

    /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
        $columns = [
            'shipmentId'     => [
                static::COLUMN_NAME     => static::t('Shipment Id'),
                static::COLUMN_TEMPLATE => 'modules/CDev/USPS/shipments/parts/cell.shipmentId.twig',
                static::COLUMN_ORDERBY  => 100,
            ],
            'trackingNumber' => [
                static::COLUMN_NAME     => static::t('Tracking number'),
                static::COLUMN_TEMPLATE => 'modules/CDev/USPS/shipments/parts/cell.trackingNumber.twig',
                static::COLUMN_ORDERBY  => 200,
            ],
            'price'          => [
                static::COLUMN_NAME     => static::t('Delivery cost'),
                static::COLUMN_TEMPLATE => 'modules/CDev/USPS/shipments/parts/cell.price.twig',
                static::COLUMN_ORDERBY  => 300,
            ],
            'labelURL'       => [
                static::COLUMN_NAME     => static::t('Shipping label'),
                static::COLUMN_TEMPLATE => 'modules/CDev/USPS/shipments/parts/cell.labelURL.twig',
                static::COLUMN_ORDERBY  => 400,
            ],
            'action'         => [
                static::COLUMN_NAME     => '',
                static::COLUMN_TEMPLATE => 'modules/CDev/USPS/shipments/parts/cell.action.twig',
                static::COLUMN_MAIN     => true,
                static::COLUMN_ORDERBY  => 500,
            ],
        ];

        return $columns;
    }

    /**
     * Define repository name
     *
     * @return string
     */
    protected function defineRepositoryName()
    {
        return 'XLite\Module\CDev\USPS\Model\Shipment';
    }

    /**
     * Get current country code
     *
     * @return string
     */
    protected function getOrderId()
    {
        return \XLite::getController()->getOrder()->getOrderId();
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_ORDER_ID => new \XLite\Model\WidgetParam\TypeString('Order', $this->getOrderId()),
        ];
    }

    /**
     * Define so called "request" parameters
     */
    protected function defineRequestParams()
    {
        parent::defineRequestParams();

        $this->requestParams = array_merge($this->requestParams, static::getSearchParams());
    }

    /**
     * Default search conditions
     *
     * @param  \XLite\Core\CommonCell $searchCase Search case
     *
     * @return \XLite\Core\CommonCell
     */
    protected function postprocessSearchCase(\XLite\Core\CommonCell $searchCase)
    {
        $searchCase = parent::postprocessSearchCase($searchCase);

        $searchCase->{\XLite\Module\CDev\USPS\Model\Repo\Shipment::SEARCH_ORDER_ID} = $this->getOrderId();

        return $searchCase;
    }

    /**
     * Default value for PARAM_WRAP_WITH_FORM
     *
     * @return boolean
     */
    protected function wrapWithFormByDefault()
    {
        return true;
    }

    /**
     * Get wrapper form action
     *
     * @return string
     */
    protected function getFormAction()
    {
        return 'updateUspsShipments';
    }

    /**
     * Get wrapper form params
     *
     * @return array
     */
    protected function getFormParams()
    {
        return array_merge(
            parent::getFormParams(),
            [
                'id'       => '',
                'order_id' => $this->getOrderId(),
            ]
        );
    }

    /**
     * Auxiliary method to check visibility
     *
     * @return boolean
     */
    protected function isDisplayWithEmptyList()
    {
        return false;
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

    /**
     * Get URL common parameters
     *
     * @return array
     */
    protected function getCommonParams()
    {
        $result             = parent::getCommonParams();
        $result['order_id'] = $this->getOrderId();

        return $result;
    }

    /**
     * @return string
     */
    protected function getWeightUnits()
    {
        return \XLite\Core\Config::getInstance()->Units->weight_unit;
    }

    /**
     * @return string
     */
    protected function getDimensionUnits()
    {
        return \XLite\Core\Config::getInstance()->Units->dim_unit;
    }

    /**
     * @return string
     */
    protected function getDimensionSymbol()
    {
        return \XLite\Core\Config::getInstance()->Units->dim_symbol;
    }

    /**
     * @param \XLite\Module\CDev\USPS\Model\Shipment $shipment
     *
     * @return array
     */
    protected function getLabelContentURLs($shipment)
    {
        $result = [];
        foreach ($shipment->getLabelContent() as $index => $label) {
            if ($label['contents']) {
                $result[$index] = $this->buildURL(
                    'order',
                    'usps_get_label',
                    [
                        'order_id' => $this->getOrderId(),
                        'id'       => $shipment->getId(),
                        'index'    => $index,
                    ]
                );
            }
        }

        return $result;
    }
}
