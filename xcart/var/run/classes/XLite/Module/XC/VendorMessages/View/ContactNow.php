<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\VendorMessages\View;

/**
 * ContactNow
 *
 * @ListChild (list="product.vendor_info.popover_content", weight="40")
 * @ListChild (list="vendorpage.vendor_info", weight="2000")
 */
class ContactNow extends \XLite\View\AView
{
    /**
     * @inheritdoc
     */
    protected function isVisible()
    {
        return parent::isVisible() && \XLite\Module\XC\VendorMessages\Main::isVendorAllowedToCommunicate();
    }

    /**
     * @inheritdoc
     */
    protected function getDefaultTemplate()
    {
        return 'modules/XC/VendorMessages/vendor/contact_now.twig';
    }

    /**
     * Return class for button
     *
     * @return string
     */
    protected function getButtonClass()
    {
        return \XLite\Core\Auth::getInstance()->isLogged()
            ? '\XLite\Module\XC\VendorMessages\View\Button\VendorContactNow'
            : '\XLite\Module\XC\VendorMessages\View\Button\ContactNowLogin';
    }

    /**
     * Return button label
     *
     * @return string
     */
    protected function getButtonLabel()
    {
        return 'Contact now';
    }

    /**
     * @inheritdoc
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        
        $list[] = array(
            'file'  => 'modules/XC/VendorMessages/vendor/contact_now.less',
            'media' => 'screen',
            'merge' => 'bootstrap/css/bootstrap.less',
        );

        return $list;
    }
}