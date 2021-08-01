<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Input\Checkbox;

/**
 * On/Off FlipSwitch with Simple value checking
 */
class OnOffLegacy extends \XLite\View\FormField\Input\Checkbox\OnOff
{
    /**
     * Determines if checkbox is checked
     *
     * @return boolean
     */
    protected function isChecked()
    {
        return (parent::isChecked() || 'Y' === $this->getValue() || '1' === $this->getValue())
            && 'N' !== $this->getValue() && '0' !== $this->getValue() && 'false' !== $this->getValue();
    }

    /**
     * getCommonAttributes
     *
     * @return array
     */
    protected function getCommonAttributes()
    {
        $list = parent::getCommonAttributes();

        unset($list['value']);

        return $list;
    }
}
