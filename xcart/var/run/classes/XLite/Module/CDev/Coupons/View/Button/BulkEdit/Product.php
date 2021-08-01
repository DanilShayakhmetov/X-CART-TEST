<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Coupons\View\Button\BulkEdit;

/**
 * @Decorator\Depend ("XC\BulkEditing")
 */
 class Product extends \XLite\Module\CDev\FileAttachments\View\Button\BulkEdit\Product implements \XLite\Base\IDecorator
{
    /**
     * @return array
     */
    protected function getScenarios()
    {
        $result = parent::getScenarios();

        $result['coupons'] = [
            'position' => 350,
        ];

        return $result;
    }
}
