<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\View;

use XLite\Core\Translation;

abstract class CommonResources extends \XLite\View\CommonResources implements \XLite\Base\IDecorator
{
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        return $list;
    }

    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        return $list;
    }

    /**
     * Register files from common repository
     *
     * @return array
     */
    protected function getCommonFiles()
    {
        $list = parent::getCommonFiles();

        $list[static::RESOURCE_CSS][]   = 'froala-editor/css/froala_style.fixed.css';

        return $list;
    }
}
