<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\FacebookMarketing\View\PixelScripts;
use XLite\Module\XC\FacebookMarketing\View\PixelScripts\CommonScripts;

/**
 * APixelScript
 */
abstract class APixelScript extends \XLite\View\AView implements \XLite\Base\IDecorator
{
    /**
     * @inheritdoc
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        if (\XLite\Module\XC\FacebookMarketing\Main::isPixelEnabled() && !\XLite::isAdminZone()) {
            $list = array_merge($list, CommonScripts::getInstance()->getFacebookPixelScripts());
        }

        return $list;
    }
}