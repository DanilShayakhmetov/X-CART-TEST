<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\View\Form;


/**
 * MailChimpHeader
 */
class MailChimpHeader extends \XLite\View\Form\Settings
{
    /**
     * @inheritdoc
     */
    protected function getDefaultTarget()
    {
        return 'mailchimp_options';
    }

    /**
     * @inheritdoc
     */
    protected function getDefaultAction()
    {
        return 'set_api_key';
    }
}