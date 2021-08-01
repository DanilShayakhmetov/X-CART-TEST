<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\SimpleCMS\Logic\RemoveData;


 class Generator extends \XLite\Module\CDev\VolumeDiscounts\Logic\RemoveData\Generator implements \XLite\Base\IDecorator
{
    protected function getStepsList()
    {
        $list = parent::getStepsList();
        $list[] = '\XLite\Module\CDev\SimpleCMS\Logic\RemoveData\Step\Pages';

        return $list;
    }
}