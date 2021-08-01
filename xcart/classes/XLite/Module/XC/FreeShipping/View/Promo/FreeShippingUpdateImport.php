<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\FreeShipping\View\Promo;


/**
 * Free shipping update import info
 *
 * @ListChild(list="import.failed.content", weight="0", zone="admin")
 */
class FreeShippingUpdateImport extends \XLite\View\Alert\Warning
{
    protected function isVisible()
    {
        return parent::isVisible()
            && $this->getImporter()
            && $this->getImporter()->isDisplayFreeShippingUpdateNotice();
    }

    protected function getAlertContent()
    {
        return static::t('Free shipping update import text');
    }
}