<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductVariants\Module\XC\ThemeTweaker\Core\Notifications;


/**
 * DataPreProcessor
 *
 * @Decorator\Depend("XC\ThemeTweaker")
 */
class DataPreProcessor extends \XLite\Module\XC\ThemeTweaker\Core\Notifications\DataPreProcessor implements \XLite\Base\IDecorator
{
    public static function prepareDataForNotification($dir, array $data)
    {
        $data = parent::prepareDataForNotification($dir, $data);

        return \XLite\Module\XC\ProductVariants\Core\Notifications\DataPreProcessor::prepareDataForNotification(
            $dir,
            $data
        );
    }
}