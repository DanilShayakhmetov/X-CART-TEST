<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Geolocation\View\FormField\Label;


class CurrentDatabase extends \XLite\View\FormField\AFormField
{
    /**
     * Return field type
     *
     * @return string
     */
    public function getFieldType()
    {
        return self::FIELD_TYPE_LABEL;
    }

    /**
     * Get label value
     *
     * @return string
     */
    protected function getLabelValue()
    {
        $path = \XLite\Core\Config::getInstance()->XC->Geolocation->extended_db_path;

        if ($path && file_exists($path)) {
            return $path;
        }

        return static::t('Default') . ': ' . \XLite\Module\XC\Geolocation\Model\Geolocation\MaxMindGeoIP::getDefaultDatabasePath();
    }

    /**
     * Return extended database path if exist else null
     *
     * @return string|null
     */
    protected function getCustomDatabasePath()
    {
        $path = \XLite\Core\Config::getInstance()->XC->Geolocation->extended_db_path;

        if ($path && file_exists($path)) {
            return $path;
        }

        return null;
    }

    /**
     * Return name of the folder with templates
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/XC/Geolocation/current_database';
    }

    /**
     * Return field template
     *
     * @return string
     */
    protected function getFieldTemplate()
    {
        return 'body.twig';
    }

    /**
     * Set the form field as "form control" (some major styling will be applied)
     *
     * @return boolean
     */
    protected function isFormControl()
    {
        return false;
    }
}