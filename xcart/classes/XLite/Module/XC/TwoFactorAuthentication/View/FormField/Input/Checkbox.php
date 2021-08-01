<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\TwoFactorAuthentication\View\FormField\Input;

class Checkbox extends \XLite\View\FormField\Input\Checkbox
{

    /**
     * Determines if checkbox is checked
     *
     * @return boolean
     */
    protected function isChecked()
    {
        return (parent::isChecked() && $this->callFormMethod('getSavedData', [$this->getName()])) || 'Y' == $this->getValue() || '1' === $this->getValue();
    }

    /**
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'modules/XC/TwoFactorAuthentication/form_field/checkbox/use2fa.js';

        return $list;
    }

    /**
     * @return array
     */
    protected function getCommonAttributes()
    {
        $list          = parent::getCommonAttributes();
        $list['value'] = '1';

        return $list;
    }

}
