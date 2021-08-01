<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XPay\XPaymentsCloud\View\ItemsList\Model;

use XLite\Model\WidgetParam\TypeString;
use XLite\Module\XPay\XPaymentsCloud\Model\Subscription\Subscription as SubscriptionModel;
use XLite\Module\XPay\XPaymentsCloud\Model\Repo\Subscription\Subscription as SubscriptionRepo;

/**
 * Subscriptions items list
 */
class Subscription extends \XLite\View\ItemsList\Model\Table
{
    /**
     * Widget param names
     */
    const PARAM_ID              = 'id';
    const PARAM_PRODUCT_NAME    = 'productName';
    const PARAM_STATUS          = 'status';
    const PARAM_DATE_RANGE      = 'dateRange';
    const PARAM_NEXT_DATE_RANGE = 'nextDateRange';

    /**
     * Allowed sort criterions
     */
    const SORT_BY_MODE_ID       = 's.id';
    const SORT_BY_MODE_DATE     = 's.startDate';
    const SORT_BY_MODE_PRODUCT  = 'initialOrderItem.name';
    const SORT_BY_MODE_CUSTOMER = 'profile.login';
    const SORT_BY_MODE_STATUS   = 's.status';
    const SORT_BY_MODE_FEE      = 's.fee';

    /**
     * Define and set widget attributes; initialize widget
     *
     * @param array $params Widget params OPTIONAL
     */
    public function __construct(array $params = [])
    {
        $this->sortByModes += [
            static::SORT_BY_MODE_ID       => 'Subscription ID',
            static::SORT_BY_MODE_DATE     => 'Date',
            static::SORT_BY_MODE_PRODUCT  => 'Product',
            static::SORT_BY_MODE_CUSTOMER => 'Profile',
            static::SORT_BY_MODE_STATUS   => 'Status',
            static::SORT_BY_MODE_FEE      => 'Fee',
        ];

        parent::__construct($params);
    }

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/XPay/XPaymentsCloud/subscription/style.css';

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
            'id'               => [
                static::COLUMN_NAME     => static::t('Subscription #'),
                static::COLUMN_TEMPLATE => 'modules/XPay/XPaymentsCloud/subscription/parts/cell.id.twig',
                static::COLUMN_SORT     => static::SORT_BY_MODE_ID,
                static::COLUMN_ORDERBY  => 100,
            ],
            'startDate'        => [
                static::COLUMN_NAME     => static::t('Date'),
                static::COLUMN_TEMPLATE => 'modules/XPay/XPaymentsCloud/subscription/parts/cell.date.twig',
                static::COLUMN_NO_WRAP  => true,
                static::COLUMN_SORT     => static::SORT_BY_MODE_DATE,
                static::COLUMN_ORDERBY  => 200,
            ],
            'product'          => [
                static::COLUMN_NAME     => static::t('Product'),
                static::COLUMN_TEMPLATE => 'modules/XPay/XPaymentsCloud/subscription/parts/cell.product.twig',
                static::COLUMN_NO_WRAP  => true,
                static::COLUMN_SORT     => static::SORT_BY_MODE_PRODUCT,
                static::COLUMN_ORDERBY  => 300,
            ],
            'profile'          => [
                static::COLUMN_NAME     => static::t('Customer'),
                static::COLUMN_TEMPLATE => 'modules/XPay/XPaymentsCloud/subscription/parts/cell.profile.twig',
                static::COLUMN_NO_WRAP  => true,
                static::COLUMN_MAIN     => true,
                static::COLUMN_SORT     => static::SORT_BY_MODE_CUSTOMER,
                static::COLUMN_ORDERBY  => 400,
            ],
            'fee'              => [
                static::COLUMN_NAME     => static::t('Fee'),
                static::COLUMN_TEMPLATE => 'modules/XPay/XPaymentsCloud/subscription/parts/cell.fee.twig',
                static::COLUMN_SORT     => static::SORT_BY_MODE_FEE,
                static::COLUMN_ORDERBY  => 500,
            ],
            'status'           => [
                static::COLUMN_NAME    => static::t('Status'),
                static::COLUMN_CLASS   => 'XLite\Module\XPay\XPaymentsCloud\View\FormField\Inline\Select\SubscriptionStatus',
                static::COLUMN_SORT    => static::SORT_BY_MODE_STATUS,
                static::COLUMN_ORDERBY => 600,
            ],
            'shipping_address' => [
                static::COLUMN_NAME     => static::t('Shipping address'),
                static::COLUMN_TEMPLATE => 'modules/XPay/XPaymentsCloud/subscription/parts/cell.shipping_address.twig',
                static::COLUMN_ORDERBY  => 650,
            ],
            'card'             => [
                static::COLUMN_NAME     => static::t('Card for payments'),
                static::COLUMN_TEMPLATE => 'modules/XPay/XPaymentsCloud/subscription/parts/cell.card.twig',
                static::COLUMN_ORDERBY  => 700,
            ],
            'statistics'       => [
                static::COLUMN_NAME     => static::t('Statistics'),
                static::COLUMN_TEMPLATE => 'modules/XPay/XPaymentsCloud/subscription/parts/cell.statistics.twig',
                static::COLUMN_ORDERBY  => 800,
            ],
        ];
    }

    /**
     * Define repository name
     *
     * @return string
     */
    protected function defineRepositoryName()
    {
        return 'XLite\Module\XPay\XPaymentsCloud\Model\Subscription\Subscription';
    }


    /**
     * Creation button position
     *
     * @return integer
     */
    protected function isCreation()
    {
        return static::CREATE_INLINE_NONE;
    }

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' subscription';
    }

    /**
     * Return class name for the list pager
     *
     * @return string
     */
    protected function getPagerClass()
    {
        return 'XLite\View\Pager\Admin\Model\Table';
    }

    /**
     * Define line class as list of names
     *
     * @param integer $index Line index
     * @param \XLite\Model\AEntity|SubscriptionModel $entity Subscription
     *
     * @return array
     */
    protected function defineLineClass($index, \XLite\Model\AEntity $entity = null)
    {
        $list = parent::defineLineClass($index, $entity);

        if ($this->isXpaymentsLastPaymentFailed($entity)) {
            $list[] = 'last-payment-failed';
        }

        if ($this->isXpaymentsLastPaymentExpired($entity)) {
            $list[] = 'last-payment-expired';
        }

        return $list;
    }

    /**
     * Preprocess profile
     *
     * @param \XLite\Model\Profile $profile Profile
     * @param array $column Column data
     * @param mixed $entity Order
     *
     * @return string
     */
    protected function preprocessProfile(\XLite\Model\Profile $profile, array $column, $entity)
    {
        $address = $profile->getBillingAddress() ?: $profile->getShippingAddress();

        return $address ? $address->getName() : $profile->getLogin();
    }

    /**
     * Check - order's profile removed or not
     *
     * @param SubscriptionModel $subscription Subscription
     *
     * @return boolean
     */
    protected function isProfileRemoved($subscription)
    {
        $order = $subscription->getInitialOrder();

        return $order
            ? (!$order->getOrigProfile() || $order->getOrigProfile()->getOrder())
            : true;
    }

    /**
     * isXpaymentsLastPaymentFailed
     *
     * @param SubscriptionModel $subscription Subscription
     *
     * @return boolean
     */
    protected function isXpaymentsLastPaymentFailed($subscription)
    {
        return SubscriptionModel::STATUS_FAILED !== $subscription->getStatus()
               && $subscription->getActualDate() > $subscription->getPlannedDate();
    }

    /**
     * isXpaymentsLastPaymentExpired
     *
     * @param SubscriptionModel $subscription Subscription
     *
     * @return boolean
     */
    protected function isXpaymentsLastPaymentExpired($subscription)
    {
        return $subscription->getActualDate() < \XLite\Module\XPay\XPaymentsCloud\Core\Converter::now();
    }

    /**
     * isXpaymentsNextDateVisible
     *
     * @param SubscriptionModel $subscription Subscription
     *
     * @return boolean
     */
    protected function isXpaymentsNextDateVisible($subscription)
    {
        return SubscriptionModel::STATUS_NOT_STARTED !== $subscription->getStatus()
               && SubscriptionModel::STATUS_FINISHED !== $subscription->getStatus()
               && SubscriptionModel::STATUS_STOPPED !== $subscription->getStatus()
               && SubscriptionModel::STATUS_FAILED !== $subscription->getStatus();
    }

    /**
     * Return search parameters
     *
     * @return array
     */
    public static function getSearchParams()
    {
        return [
            SubscriptionRepo::SEARCH_ID              => static::PARAM_ID,
            SubscriptionRepo::SEARCH_PRODUCT_NAME    => static::PARAM_PRODUCT_NAME,
            SubscriptionRepo::SEARCH_STATUS          => static::PARAM_STATUS,
            SubscriptionRepo::SEARCH_DATE_RANGE      => static::PARAM_DATE_RANGE,
            SubscriptionRepo::SEARCH_NEXT_DATE_RANGE => static::PARAM_NEXT_DATE_RANGE,
        ];
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
            static::PARAM_ID              => new TypeString('Subscription ID', ''),
            static::PARAM_PRODUCT_NAME    => new TypeString('Product', ''),
            static::PARAM_STATUS          => new TypeString('Status', ''),
            static::PARAM_DATE_RANGE      => new TypeString('Date range', ''),
            static::PARAM_NEXT_DATE_RANGE => new TypeString('Next date range', ''),
        ];
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
     * Return params list to use for search
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchCondition()
    {
        $result = parent::getSearchCondition();

        // We initialize structure to define order (field and sort direction) in search query.
        $result->{SubscriptionRepo::SEARCH_ORDER_BY} = $this->getOrderBy();

        foreach (static::getSearchParams() as $modelParam => $requestParam) {
            $result->$modelParam = $this->getParam($requestParam);
        }

        return $result;
    }

    /**
     * Return subscriptions list
     *
     * @param \XLite\Core\CommonCell $cnd Search condition
     * @param boolean $countOnly Return items list or only its size OPTIONAL
     *
     * @return array|integer
     */
    protected function getData(\XLite\Core\CommonCell $cnd, $countOnly = false)
    {
        return \XLite\Core\Database::getRepo('\XLite\Module\XPay\XPaymentsCloud\Model\Subscription\Subscription')
            ->search($cnd, $countOnly);
    }

    /**
     * getSortByModeDefault
     *
     * @return string
     */
    protected function getSortByModeDefault()
    {
        return static::SORT_BY_MODE_DATE;
    }

    /**
     * getSortOrderDefault
     *
     * @return string
     */
    protected function getSortOrderModeDefault()
    {
        return static::SORT_ORDER_DESC;
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
     * Disable remove button for subscriptions
     *
     * @param \XLite\Model\AEntity $entity Shipping method object
     *
     * @return boolean
     */
    protected function isAllowEntityRemove(\XLite\Model\AEntity $entity)
    {
        /** @var SubscriptionModel $entity */
        return false;
    }

    /**
     * Get formatted card name
     *
     * @param array $card Card details
     *
     * @return string
     */
    protected function getCardName($card)
    {
        $type = $card['type'] . str_repeat('&nbsp;', 4 - strlen($card['type']));

        return $type . ' ' . $card['cardNumber'] . ' ' . $card['expire'];
    }

    /**
     * Get formatted address name
     *
     * @param \XLite\Model\Address $address Address
     *
     * @return string
     */
    protected function getAddressName(\XLite\Model\Address $address = null)
    {
        return (!is_null($address))
            ? $address->getStreet() . ', ' . $address->getCity() . ', ' . $address->getCountry()->getCode() . ', ' . $address->getZipcode()
            : '';
    }

    /**
     * @return string
     */
    protected function getUpdateMessage()
    {
        return static::t('Subscriptions successfully updated');
    }

}