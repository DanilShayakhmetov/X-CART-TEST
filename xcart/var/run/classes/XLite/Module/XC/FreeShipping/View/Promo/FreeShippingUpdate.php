<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\FreeShipping\View\Promo;

use XLite\Core\Config;
use XLite\Core\Database;

/**
 * Free shipping update info
 *
 * @ListChild (list="crud.modulesettings.header", zone="admin", weight="100")
 */
class FreeShippingUpdate extends \XLite\View\Alert\Warning
{
    public static function getAllowedTargets()
    {
        return array_merge(parent::getAllowedTargets(), [
            'module'
        ]);
    }

    /**
     * @param string $template
     * @param array  $profilerData
     *
     * @throws \Exception
     */
    protected function finalizeTemplateDisplay($template, array $profilerData)
    {
        parent::finalizeTemplateDisplay($template, $profilerData);

        Database::getRepo('XLite\Model\Config')->createOption([
            'name'     => 'display_update_info',
            'category' => 'XC\\FreeShipping',
            'value'    => false,
        ]);
    }

    protected function isVisible()
    {
        return parent::isVisible()
            && $this->getModule() === 'XC\\FreeShipping'
            && Config::getInstance()->XC->FreeShipping->display_update_info !== null;
    }

    protected function getAlertContent()
    {
        return static::t('Free shipping update text');
    }
}