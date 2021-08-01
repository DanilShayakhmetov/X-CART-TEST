<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CrispWhiteSkin\View;

/**
 * Contact Us
 *
 * @Decorator\Depend ("CDev\ContactUs")
 * @ListChild (list="contact_us.parts.last", zone="customer", weight="10")
 */
class ContactsContactUsFooter extends \XLite\View\AView
{
    /**
     * @return boolean
     */
    protected function isVisible()
    {
        return !\XLite\Core\Config::getInstance()->CDev->ContactUs->showEmail
            && parent::isVisible();
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'contact_us/parts/contact_us.address.contact_us_link.twig';
    }
}
