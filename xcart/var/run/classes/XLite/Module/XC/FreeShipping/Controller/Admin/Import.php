<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\FreeShipping\Controller\Admin;


 class Import extends \XLite\Controller\Admin\ImportAbstract implements \XLite\Base\IDecorator
{
    protected function doActionProceed()
    {
        if (
            $this->getImporter()
            && $this->getImporter()->getOptions()->offsetExists('displayFreeShippingUpdateNotification')
            && $this->getImporter()->getOptions()->offsetGet('displayFreeShippingUpdateNotification')
        ) {
            $this->getImporter()->getOptions()->displayFreeShippingUpdateNotification = false;
        }

        parent::doActionProceed();
    }
}