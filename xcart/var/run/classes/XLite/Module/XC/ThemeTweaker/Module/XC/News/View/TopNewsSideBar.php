<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\Module\XC\News\View;

use XLite\Module\XC\ThemeTweaker;

/**
 * @Decorator\Depend ("XC\News")
 */
abstract class TopNewsSideBar extends \XLite\Module\XC\News\View\TopNewsSideBarAbstract implements ThemeTweaker\View\LayoutBlockInterface, \XLite\Base\IDecorator
{
    use ThemeTweaker\View\LayoutBlockTrait;
}
