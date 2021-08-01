<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\FileAttachments\View\FormField\Select;
use XLite\Module\CDev\FileAttachments\Model\Product\Attachment;

/**
 * Memberships selector
 */
class AttributeMembership extends \XLite\View\FormField\Select\Membership
{
    /**
     * getDefaultOptions
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return [
            Attachment::ACCESS_ANY => static::t('All customers'),
            Attachment::ACCESS_REGISTERED => static::t('Registered Customers'),
        ]
        + $this->getMembershipsList();
    }
}