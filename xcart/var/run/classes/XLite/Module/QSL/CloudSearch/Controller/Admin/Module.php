<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
namespace XLite\Module\QSL\CloudSearch\Controller\Admin;

use XLite\Module\QSL\CloudSearch\Main;

/**
 * Module settings
 */
 class Module extends \XLite\Module\XC\Geolocation\Controller\Admin\Module implements \XLite\Base\IDecorator
{
    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        if ($this->getModuleId() === 'QSL-CloudSearch') {
            return Main::isXCCloud()
                ? static::t('Search & Filter Dashboard')
                : static::t('X module settings', ['name' => 'CloudSearch & CloudFilters']);
        }

        return parent::getTitle();
    }
}
