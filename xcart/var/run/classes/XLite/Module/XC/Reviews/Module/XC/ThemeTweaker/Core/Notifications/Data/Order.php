<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Reviews\Module\XC\ThemeTweaker\Core\Notifications\Data;


/**
 * Order
 *
 * @Decorator\Depend("XC\ThemeTweaker")
 */
 class Order extends \XLite\Module\XC\ThemeTweaker\Core\Notifications\Data\OrderAbstract implements \XLite\Base\IDecorator
{
    protected function getTemplateDirectories()
    {
        return array_merge(parent::getTemplateDirectories(), [
            'modules/XC/Reviews/review_key',
        ]);
    }
}