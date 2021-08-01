<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\View;

/**
 * Abstract widget
 */
abstract class AView extends \XLite\View\AView implements \XLite\Base\IDecorator
{
    /**
     * Return theme common files
     *
     * @param boolean $adminZone Admin zone flag OPTIONAL
     *
     * @return array
     */
    protected function getThemeFiles($adminZone = null)
    {
        $list = parent::getThemeFiles($adminZone);

        if (\XLite\Core\Config::getInstance()->XC->MailChimp->mcjs) {

            $list[static::RESOURCE_CSS][] = 'modules/XC/MailChimp/body/parts/mcjs.css';
        }

        return $list;
    }
}
