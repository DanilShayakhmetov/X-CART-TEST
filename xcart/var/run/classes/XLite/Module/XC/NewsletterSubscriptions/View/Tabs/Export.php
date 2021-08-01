<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\NewsletterSubscriptions\View\Tabs;

/**
 * Tabs related to export page
 */
 class Export extends \XLite\Module\XC\Reviews\View\Tabs\Export implements \XLite\Base\IDecorator
{
    /**
     * Return widget default template
     *
     * @return string
     */
    protected function defineSections()
    {
        return parent::defineSections() + [
                'XLite\Module\XC\NewsletterSubscriptions\Logic\Export\Step\NewsletterSubscribers' => 'Subscribers',
            ];
    }
}
