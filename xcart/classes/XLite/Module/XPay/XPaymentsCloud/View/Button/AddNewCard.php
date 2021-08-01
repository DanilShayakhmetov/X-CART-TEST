<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XPay\XPaymentsCloud\View\Button;


/**
 * Add new card button widget
 */
class AddNewCard extends \XLite\View\Button\APopupButton
{
    /*
     * Widget parameter
     */
    const PARAM_PROFILE_ID = 'profileId';
    const PARAM_WIDGET_TITLE = 'widgetTitle';
    const PARAM_AMOUNT = 'amount';

    /**
     * Get JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'modules/XPay/XPaymentsCloud/button/add_new_card.js';

        return $list;
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
            self::PARAM_PROFILE_ID => new \XLite\Model\WidgetParam\TypeInt('Profile ID', 0),
            self::PARAM_WIDGET_TITLE => new \XLite\Model\WidgetParam\TypeString('Widget title', static::t('Card setup')),
            self::PARAM_AMOUNT => new \XLite\Model\WidgetParam\TypeInt('Card Setup Amount', 0),
        );
    }

    /**
     * Return URL parameters to use in AJAX popup
     *
     * @return array
     */
    protected function prepareURLParams()
    {
        return array(
            'target'       => 'xpayments_cards',
            'profile_id'   => $this->getParam(self::PARAM_PROFILE_ID),
            'widget'       => '\XLite\Module\XPay\XPaymentsCloud\View\CardSetup',
            'widget_title' => $this->getParam(self::PARAM_WIDGET_TITLE),
            'amount'       => $this->getParam(self::PARAM_AMOUNT),
        );
    }

    /**
     * Return CSS classes
     *
     * @return string
     */
    protected function getClass()
    {
        return 'btn regular-button popup-button add-new-card';
    }
}
