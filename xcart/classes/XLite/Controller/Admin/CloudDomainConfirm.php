<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

use XLite\Core\Mail\Common\ChangeCloudDomain;

class CloudDomainConfirm extends \XLite\Controller\Admin\AAdmin
{
    /**
     * @return string|null
     */
    public function getTitle()
    {
        return static::t('Domain name transfer');
    }
}
