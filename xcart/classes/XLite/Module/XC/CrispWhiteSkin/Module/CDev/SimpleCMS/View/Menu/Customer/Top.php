<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CrispWhiteSkin\Module\CDev\SimpleCMS\View\Menu\Customer;

use XLite\Core\PreloadedLabels\ProviderInterface;

/**
 * Primary menu
 * @Decorator\Depend ("CDev\SimpleCMS")
 */
class Top extends \XLite\View\Menu\Customer\Top implements \XLite\Base\IDecorator, ProviderInterface
{
    /**
     * @return array
     */
    public function getJSFiles()
    {
        $list   = parent::getJSFiles();
        $list[] = 'modules/CDev/SimpleCMS/top_menu.js';

        return $list;
    }

    /**
     * Array of labels in following format.
     *
     * 'label' => 'translation'
     *
     * @return mixed
     */
    public function getPreloadedLanguageLabels()
    {
        return [
            'More' => static::t('More'),
        ];
    }
}
