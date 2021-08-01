<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\FacebookMarketing\View;


abstract class APixel extends \XLite\View\AView implements \XLite\Base\IDecorator
{
    /**
     * Register files from common repository
     *
     * @return array
     */
    protected function getCommonFiles()
    {
        $list = parent::getCommonFiles();

        if (\XLite\Module\XC\FacebookMarketing\Main::isPixelEnabled()) {
            $list[static::RESOURCE_JS][] = 'modules/XC/FacebookMarketing/pixel_core.js';
            $list[static::RESOURCE_JS][] = 'modules/XC/FacebookMarketing/pixel_event.js';
        }

        return $list;
    }
}