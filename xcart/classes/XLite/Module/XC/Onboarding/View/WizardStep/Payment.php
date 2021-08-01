<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Onboarding\View\WizardStep;


/**
 * Payment
 */
class Payment extends \XLite\Module\XC\Onboarding\View\AWizardStep
{
    /**
     * @return string
     */
    protected function getMoreSettingsLocation()
    {
        return $this->buildURL('payment_settings', '', [
            'show_add_payment_popup' => 1,
        ]);
    }

    /**
     * @return array
     */
    protected function getOnlineWidgets()
    {
        return [];
    }

    /**
     * @return array
     */
    protected function getOnlineWidgetsSorted()
    {
        $widgets = $this->getOnlineWidgets();

        ksort($widgets);

        return $widgets;
    }
}