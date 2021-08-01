<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XPay\XPaymentsCloud\View\Form;

/**
 * Customer's address selector and payment form at add new card page
 */
class CardSetup extends \XLite\View\Form\AForm
{

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        return $list;
    }

    /**
     * Register files from common repository
     *
     * @return array
     */
    public function getCommonFiles()
    {
        $list = parent::getCommonFiles();
        return $list;
    }

    /**
     * getDefaultTarget
     *
     * @return string
     */
    protected function getDefaultTarget()
    {
        return 'xpayments_cards';
    }

    /**
     * Get default action
     *
     * @return string
     */
    protected function getDefaultAction()
    {
        return 'card_setup';
    }

    /**
     * Get customer profile id
     *
     * @return integer
     */
    protected function getCustomerProfileId()
    {
        if (\XLite::isAdminZone()) {
            $profileId = \XLite\Core\Request::getInstance()->profile_id;
        }
        if (empty($profileId)) {
            $profileId = \XLite\Core\Auth::getInstance()->getProfile()->getProfileId();
        }
        return $profileId;
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams[self::PARAM_CLASS_NAME]->setValue('card-setup');
    }

    /**
     * Return list of the form default parameters
     *
     * @return array
     */
    protected function getDefaultParams()
    {
        $params = [];

        if (\XLite::isAdminZone()) {
            $params['profile_id'] = $this->getCustomerProfileId();
        };

        return $params;

    }

}
