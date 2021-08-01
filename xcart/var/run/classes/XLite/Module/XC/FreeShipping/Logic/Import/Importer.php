<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\FreeShipping\Logic\Import;


use XLite\Core\Config;

 class Importer extends \XLite\Module\XC\NewsletterSubscriptions\Logic\Import\Importer implements \XLite\Base\IDecorator
{
    public function isNextStepAllowed()
    {
        return parent::isNextStepAllowed()
            && !$this->isDisplayFreeShippingUpdateNotice();
    }

    public function isDisplayFreeShippingUpdateNotice()
    {
        return Config::getInstance()->XC->FreeShipping->display_update_import_info
            && $this->getOptions()->offsetExists('displayFreeShippingUpdateNotification')
            && $this->getOptions()->offsetGet('displayFreeShippingUpdateNotification');
    }
}