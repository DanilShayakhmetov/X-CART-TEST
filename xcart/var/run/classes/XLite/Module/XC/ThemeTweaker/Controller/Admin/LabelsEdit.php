<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\Controller\Admin;

/**
 * ThemeTweaker controller
 */
class LabelsEdit extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Define the actions with no secure token
     *
     * @return array
     */
    public static function defineFreeFormIdActions()
    {
        $list = parent::defineFreeFormIdActions();
        $list[] = 'apply_changes';
        $list[] = 'disable';

        return $list;
    }

    protected function doActionUpdateLabel()
    {
        $name = \XLite\Core\Request::getInstance()->name;
        $translation = \XLite\Core\Request::getInstance()->translation;
        $code = \XLite\Core\Request::getInstance()->code;

        if ($name && $translation && $code) {

        }

        $this->set('silent', true);
        $this->setSuppressOutput(true);
    }
}
