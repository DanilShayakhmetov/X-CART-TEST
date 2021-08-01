<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\USPS\View\FormField\Select;

/**
 * First class mail type selector for settings page
 */
class FirstClassMailType extends \XLite\View\FormField\Select\Regular
{
    /**
     * Get default options for selector
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return array(
            'LETTER'          => 'Letter',
            'LARGE ENVELOPE'  => 'Large Envelope',
            'PARCEL'          => 'Parcel',
            'POSTCARD'        => 'Postcard',
            'LARGE POSTCARD'  => 'Large Postcard',
            'PACKAGE SERVICE' => 'Package service',
        );
    }
}
