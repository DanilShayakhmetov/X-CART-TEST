<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\VendorMessages\View\Button;


class ContactNowLogin extends \XLite\View\Button\PopupLoginLink
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
     * @inheritdoc
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        if (\XLite\Module\XC\VendorMessages\Main::isMultivendor()) {
            $list[] = 'modules/XC/VendorMessages/button/multivendor_popover.js';
        }

        return $list;
    }

    /**
     * @inheritdoc
     */
    protected function getDefaultTemplate()
    {
        return 'modules/XC/VendorMessages/button/message_popup_link.twig';
    }

    /**
     * @inheritdoc
     */
    protected function getClass()
    {
        return parent::getClass() . ' vendor-contact-now vendor-info__contact-now do-not-trigger-popover';
    }

    /**
     * Return URL parameters to use in AJAX popup
     *
     * @return array
     */
    protected function prepareURLParams()
    {
        return array_merge(parent::prepareURLParams(), [
            'fromURL' => $this->getLocationURL(),
        ]);
    }
}