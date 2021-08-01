<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select;

/**
 * Time zone selector
 */
class TimeZone extends \XLite\View\FormField\Select\Regular
{
    use \XLite\Core\Cache\ExecuteCachedTrait;

    /**
     * Get default options
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return $this->executeCachedRuntime(function () {
            $list = \DateTimeZone::listIdentifiers();

            return array_combine($list, array_map(function ($timezone) {
                $offset = (new \DateTimeZone($timezone))->getOffset(new \DateTime());

                $offset = ($offset < 0 ? '-' : '+') . gmdate('G:i', abs($offset));

                return "(UTC{$offset}) " . $timezone;
            }, $list));
        });
    }

    /**
     * Check - current value is selected or not
     *
     * @param mixed $value Value
     *
     * @return boolean
     */
    protected function isOptionSelected($value)
    {
        return $this->getValue() ? parent::isOptionSelected($value) : $value == date_default_timezone_get();
    }

}
