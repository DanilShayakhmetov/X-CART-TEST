<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\View;


/**
 * Theme tweaker template page view
 */
 class Mailer extends \XLite\View\MailerAbstract implements \XLite\Base\IDecorator
{
    public function getNotificationEditableContent($interface)
    {
        return $this->compile('modules/XC/ThemeTweaker/common/layout.twig', $interface, true);
    }

    public function getNotificationPreviewContent($interface)
    {
        return $this->compile($this->get('layoutTemplate'), $interface, true);
    }
}