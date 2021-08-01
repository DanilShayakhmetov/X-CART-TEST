<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\Core;

/**
 * Layout manager
 *
 * @Decorator\Depend ("XC\WebmasterKit")
 */
class Profiler extends \XLite\Module\XC\WebmasterKit\Core\Profiler implements \XLite\Base\IDecorator
{
    /**
     * Check - templates profiling mode is enabled or not
     *
     * @return boolean
     */
    public static function markTemplatesEnabled()
    {
        return parent::markTemplatesEnabled() && !ThemeTweaker::getInstance()->isInWebmasterMode();
    }
}
