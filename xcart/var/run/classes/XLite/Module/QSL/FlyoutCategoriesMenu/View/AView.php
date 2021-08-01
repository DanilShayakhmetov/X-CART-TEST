<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\FlyoutCategoriesMenu\View;

/**
 * Color schemes adds
 */
abstract class AView extends \XLite\Module\XC\FacebookMarketing\View\APixel implements \XLite\Base\IDecorator
{
    /**
     * Return module common file
     *
     * @param boolean $adminZone Admin zone flag OPTIONAL
     *
     * @return array
     */
    protected function getThemeFiles($adminZone = null)
    {
        $list = parent::getThemeFiles($adminZone);

        if (!(null === $adminZone ? \XLite::isAdminZone() : $adminZone)) {
            $list[static::RESOURCE_CSS][] = 'modules/QSL/FlyoutCategoriesMenu/flyout-menu.css';
            $list[static::RESOURCE_JS][]  = 'modules/QSL/FlyoutCategoriesMenu/flyout-menu.js';

            $list[static::RESOURCE_CSS][] = [
                'file'  => 'modules/QSL/FlyoutCategoriesMenu/css/grid.less',
                'media' => 'screen',
                'merge' => 'bootstrap/css/bootstrap.less',
            ];
        }

        return $list;
    }
}