<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Label;


/**
 * ReplyToCustomer
 */
class ReplyToCustomer extends \XLite\View\FormField\Label
{
    protected function getLabelValue()
    {
        return static::t('Email from the contact information section according to the type of email message', [
            'url' => $this->getURL()
        ]);
    }

    /**
     * @return string
     */
    protected function getURL()
    {
        return $this->buildURL('settings', '', [
                'page' => 'Company'
            ]) . '#contacts';
    }
}