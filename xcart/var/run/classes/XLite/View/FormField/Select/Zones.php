<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select;

/**
 * Zones selector
 */
class Zones extends \XLite\View\FormField\Select\Multiple
{
    use Select2Trait {
        getValueContainerClass as getSelect2ValueContainerClass;
    }

    /**
     * @return string
     */
    protected function getValueContainerClass()
    {
        $class = $this->getSelect2ValueContainerClass();

        $class .= ' input-zones-select2';

        return $class;
    }

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list   = parent::getJSFiles();
        $list[] = $this->getDir() . '/select/zones.js';

        return $list;
    }

    /**
     * @return mixed
     */
    protected function getPlaceholderLabel()
    {
        return static::t('All');
    }

    /**
     * Get zones list
     *
     * @return array
     */
    protected function getZonesList()
    {
        $list = array();
        foreach (\XLite\Core\Database::getRepo('\XLite\Model\Zone')->findAllZones() as $zone) {
            $list[$zone->getZoneId()] = $zone->getZoneName();
        }

        return $list;
    }

    /**
     * Get default options
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return $this->getZonesList();
    }
}
