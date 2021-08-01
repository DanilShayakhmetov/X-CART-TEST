<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\VendorMessages\View\Button;


class VendorContactNow extends \XLite\View\Button\Link
{
    const PARAM_VENDOR = 'vendor';

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            self::PARAM_VENDOR => new \XLite\Model\WidgetParam\TypeObject('Vendor'),
        ];
    }

    /**
     * Return vendor
     *
     * @return \XLite\Model\Profile
     */
    protected function getVendor()
    {
        $vendor = $this->getParam(static::PARAM_VENDOR);

        if (!$vendor && $this->getProduct()) {
            $vendor = $this->getProduct()->getVendor();
        }

        return $vendor;
    }

    /**
     * Return vendor id
     *
     * @return integer
     */
    protected function getVendorId()
    {
        return $this->getVendor()
            ? $this->getVendor()->getProfileId()
            : null;
    }

    /**
     * We make the full location path for the provided URL
     *
     * @return string
     */
    protected function getLocationURL()
    {
        return $this->buildURL(
            'conversation',
            'contact_vendor',
            ['vendor_id' => $this->getVendorId()]
        );
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/XC/VendorMessages/button/message_link.twig';
    }

    /**
     * Return CSS classes
     *
     * @return string
     */
    protected function getClass()
    {
        return 'popup-button vendor-contact-now vendor-info__contact-now do-not-trigger-popover';
    }
}