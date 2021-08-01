<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select;

class PercentFormat extends \XLite\View\FormField\Select\Regular
{
    /**
     * Get default options list
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        $result = [];
        foreach ($this->getFormats() as $format) {
            $result[$format] = sprintf($format, 50);
        }

        return $result;
    }

    /**
     * @return array
     */
    protected function getFormats()
    {
        return ['%s %%', '%s%%', '%%%s'];
    }
}
