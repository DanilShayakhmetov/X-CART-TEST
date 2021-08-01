<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\FreeShipping\View\Import;


class FreeShippingUpdateNotification extends \XLite\View\AView
{
    protected function getDefaultTemplate()
    {
        return 'modules/XC/FreeShipping/import/update_notification.twig';
    }

    public function getCSSFiles()
    {
        return array_merge(parent::getCSSFiles(), [
            'modules/XC/FreeShipping/import/update_notification.less'
        ]);
    }
}