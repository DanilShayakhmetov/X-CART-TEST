<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\USPS\View\FormField\Select;

use XLite\Module\CDev\USPS\Model\Shipping\PBAPI\Helper;

/**
 * Use rate type selector for settings page
 */
class PBInternationalParcelType extends \XLite\View\FormField\Select\Regular
{
    /**
     * Get default options for selector
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        $options = ['FRB', 'FRE', 'LGENV', 'LFRB', 'LGLFRENV', 'LTR', 'PKG', 'PFRENV'];
        $parcelTypes = Helper::getParcelTypes();

        $result = [];
        foreach ($options as $option) {
            $result[$option] = $parcelTypes[$option];
        }

        return $result;
    }
}
