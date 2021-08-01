<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ItemsList\Model\Payment;

/**
 * Methods items list
 *
 * @ListChild (list="add_payment", zone="admin", weight="10")
 */
class OnlineMethods extends \XLite\View\ItemsList\Model\Table
{
    /**
     * Widget params
     */
    const PARAM_SUBSTRING = 'substring';
    const PARAM_COUNTRY   = 'country';

    /**
     * Get search panel widget class
     *
     * @return string
     */
    protected function getSearchPanelClass()
    {
        return '\XLite\View\SearchPanel\Payment\Admin\Main';
    }

    /**
     * Check - table header is visible or not
     *
     * @return boolean
     */
    protected function isHeaderVisible()
    {
        return true;
    }

    /**
     * Return specific CSS class for dialog wrapper
     *
     * @return string
     */
    protected function getDialogCSSClass()
    {
        return parent::getDialogCSSClass() . ' payment-gateways';
    }

    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        return array_merge(
            parent::getAllowedTargets(),
            array(
                'payment_method_selection',
            )
        );
    }

    /**
     * Get a list of JavaScript files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = $this->getDir() . '/' . \XLite\View\ItemsList\Model\Table::getPageBodyDir() . '/controller.js';
        $list[] = $this->getDir() . '/' . $this->getPageBodyDir() . '/controller.js';

        return $list;
    }

    /**
     * Get default payment method country
     *
     * @return string
     */
    public static function getDefaultCountry()
    {
        return \XLite\Core\Config::getInstance()->Company->location_country;
    }

    /**
     * Get pager class
     *
     * @return string
     */
    protected function getPagerClass()
    {
        return 'XLite\View\Pager\Admin\Model\PaymentMethod\Table';
    }

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            static::PARAM_SUBSTRING => new \XLite\Model\WidgetParam\TypeString(
                'Substring', ''
            ),
            static::PARAM_COUNTRY => new \XLite\Model\WidgetParam\TypeString(
                'Country code', static::getDefaultCountry()
            ),
        );
    }

    /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
        return array(
            'name' => array(
                static::COLUMN_NAME    => static::t('Payment method'),
                static::COLUMN_ORDERBY  => 100,
            ),
        );
    }

    /**
     * Define repository name
     *
     * @return string
     */
    protected function defineRepositoryName()
    {
        return 'XLite\Model\Payment\Method';
    }

    // {{{ Behaviors

    /**
     * Mark list as removable
     *
     * @return boolean
     */
    protected function isRemoved()
    {
        return false;
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
     * Return dir which contains the page body template
     *
     * @return string
     */
    protected function getPageBodyDir()
    {
        return 'payment/online_methods';
    }

    // }}}

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' payment-methods';
    }

    /**
     * Return params list to use for search
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchCondition()
    {
        $countryCode = trim($this->getParam(static::PARAM_COUNTRY));

        \XLite\Core\Marketplace::getInstance()->updatePaymentMethods($countryCode);

        $result = parent::getSearchCondition();

        $result->{\XLite\Model\Repo\Payment\Method::P_ORDER_BY_FORCE} = array(
            'adminPosition, countryPosition.adminPosition, translations.name', 'asc'
        );
        $result->{\XLite\Model\Repo\Payment\Method::P_TYPE} = array(
            \XLite\Model\Payment\Method::TYPE_ALLINONE,
            \XLite\Model\Payment\Method::TYPE_CC_GATEWAY,
            \XLite\Model\Payment\Method::TYPE_ALTERNATIVE,
        );

        $result->{\XLite\Model\Repo\Payment\Method::P_EX_COUNTRY} = $countryCode;

        return $result;
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
        $serchCase = parent::postprocessSearchCase($searchCase);

        if ($serchCase->{\XLite\Model\Repo\Payment\Method::P_COUNTRY} === null) {
            $serchCase->{\XLite\Model\Repo\Payment\Method::P_COUNTRY} = static::getDefaultCountry();
        }

        return $searchCase;
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
     * Return search parameters.
     *
     * @return array
     */
    static public function getSearchParams()
    {
        return array(
            \XLite\Model\Repo\Payment\Method::P_NAME        => static::PARAM_SUBSTRING,
            \XLite\Model\Repo\Payment\Method::P_COUNTRY     => static::PARAM_COUNTRY,
        );
    }

    /**
     * Define so called "request" parameters
     *
     * @return void
     */
    protected function defineRequestParams()
    {
        parent::defineRequestParams();

        $this->requestParams = array_merge($this->requestParams, static::getSearchParams());
    }

    /**
     * Return "empty list" catalog
     *
     * @return string
     */
    protected function getEmptyListDir()
    {
        return parent::getEmptyListDir() . '/' . $this->getPageBodyDir();
    }


    /**
     * Get message on empty search results
     *
     * @return string
     */
    public function getNoPaymentMethodsFoundMessage()
    {
        $searchRequestParams = $this->getSearchRequestParams();

        if (!empty($searchRequestParams['country'])) {
            $country = \XLite\Core\Database::getRepo('XLite\Model\Country')->findOneBy(
                array(
                    'code' => $searchRequestParams['country']
                )
            );
        }

        return static::t(
            'No payment methods found based on the selected criteria',
            array(
                'substring' => $searchRequestParams['name'],
                'country'   => !empty($country) ? $country->getCountry() : static::t('All countries'),
            )
        );
    }

    public function getServiceToolPaymentMethodsURL()
    {
        $searchRequestParams = $this->getSearchRequestParams();

        return \XLite::getInstance()->getServiceURL('#/available-addons', null, ['tag' => 'Payment processing', 'search' => $searchRequestParams['name']]);
    }

    /**
     * URL of the X-Cart company's Contact Us page
     *
     * @return string
     */
    protected function getContactUsURL()
    {
        return \XLite\Core\Marketplace::getContactUsURL();
    }
}
