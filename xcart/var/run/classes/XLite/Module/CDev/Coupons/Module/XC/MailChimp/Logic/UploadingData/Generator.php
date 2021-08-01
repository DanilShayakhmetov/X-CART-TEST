<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Coupons\Module\XC\MailChimp\Logic\UploadingData;


/**
 * Generator
 *
 * @Decorator\Depend("XC\MailChimp")
 */
 class Generator extends \XLite\Module\XC\MailChimp\Logic\UploadingData\GeneratorAbstract implements \XLite\Base\IDecorator
{
    protected function getStepsList()
    {
        return array_merge(parent::getStepsList(), [
            'XLite\Module\CDev\Coupons\Module\XC\MailChimp\Logic\UploadingData\Step\Coupons'
        ]);
    }
}