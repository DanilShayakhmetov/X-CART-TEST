<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\TwoFactorAuthentication\View\FormField\Input\Text;

/**
 * SMSCode number
 */
class SMSCode extends \XLite\View\FormField\Input\Text\Phone
{

    /**
     * Prepare request data (typecasting)
     *
     * @param mixed $value Value
     *
     * @return mixed
     */
    public function prepareRequestData($value)
    {
        $value = parent::prepareRequestData($value);

        return preg_replace('/\s/', '', $value);
    }

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'modules/XC/TwoFactorAuthentication/form_field/select/sms_code.js';

        return $list;
    }

    /**
     * @param array $classes Classes
     *
     * @return array
     */
    public function assembleClasses(array $classes)
    {
        $classes   = parent::assembleClasses($classes);
        $classes[] = 'field-auth_sms_code';

        return $classes;
    }

}
