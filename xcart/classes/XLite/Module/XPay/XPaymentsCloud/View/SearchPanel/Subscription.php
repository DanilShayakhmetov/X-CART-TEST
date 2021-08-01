<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XPay\XPaymentsCloud\View\SearchPanel;

use \XLite\Module\XPay\XPaymentsCloud\View\FormField\Select\SubscriptionStatus as FormFieldSubscriptionStatus;

/**
 * Main admin product search panel
 */
class Subscription extends \XLite\View\SearchPanel\ASearchPanel
{
    /**
     * Get form class
     *
     * @return string
     */
    protected function getFormClass()
    {
        return 'XLite\Module\XPay\XPaymentsCloud\View\Form\Search\Subscription';
    }

    /**
     * Define the items list CSS class with which the search panel must be linked
     *
     * @return string
     */
    protected function getLinkedItemsList()
    {
        return parent::getLinkedItemsList() . '.widget.items-list.subscription';
    }

    /**
     * Define conditions
     *
     * @return array
     */
    protected function defineConditions()
    {
        return parent::defineConditions() + [
                'id'          => [
                    static::CONDITION_CLASS                             => 'XLite\View\FormField\Input\Text',
                    \XLite\View\FormField\Input\Text::PARAM_PLACEHOLDER => static::t('Order or Subscription ID'),
                    \XLite\View\FormField\AFormField::PARAM_FIELD_ONLY  => true,
                ],
                'productName' => [
                    static::CONDITION_CLASS                             => 'XLite\View\FormField\Input\Text',
                    \XLite\View\FormField\Input\Text::PARAM_PLACEHOLDER => static::t('Product name'),
                    \XLite\View\FormField\AFormField::PARAM_FIELD_ONLY  => true,
                ],
                'status'      => [
                    static::CONDITION_CLASS                                    => 'XLite\Module\XPay\XPaymentsCloud\View\FormField\Select\SubscriptionStatus',
                    FormFieldSubscriptionStatus::PARAM_DISPLAY_SEARCH_STATUSES => true,
                    \XLite\View\FormField\AFormField::PARAM_FIELD_ONLY         => true,
                ],
            ];
    }

    /**
     * Define hidden conditions
     *
     * @return array
     */
    protected function defineHiddenConditions()
    {
        return parent::defineHiddenConditions() + [
                'dateRange'     => [
                    static::CONDITION_CLASS                                 => '\XLite\View\FormField\Input\Text\DateRange',
                    \XLite\View\FormField\Input\Text\DateRange::PARAM_LABEL => static::t('Date of purchase'),
                ],
                'nextDateRange' => [
                    static::CONDITION_CLASS                                 => '\XLite\View\FormField\Input\Text\DateRange',
                    \XLite\View\FormField\Input\Text\DateRange::PARAM_LABEL => static::t('Date of the next payment'),
                ],
            ];
    }

}
