<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Input\Checkbox;


class UseSmtp extends \XLite\View\FormField\Input\Checkbox\OnOff
{
    public function getJSFiles()
    {
        return array_merge(parent::getJSFiles(), [
            'form_field/input/checkbox/use_smtp.js'
        ]);
    }
}