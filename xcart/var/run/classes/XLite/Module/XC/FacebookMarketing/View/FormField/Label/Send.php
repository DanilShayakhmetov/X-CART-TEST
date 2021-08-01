<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\FacebookMarketing\View\FormField\Label;


class Send extends \XLite\View\FormField\AFormField
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
     * Return text of Help tooltip
     *
     * @return string
     */

    public function getHelpText()
    {
        return static::t('Your product feed will be automatically sent to Facebook every 24 hours at the same time your Facebook account was connected to your X-Cart store.');
    }

    /**
     * Return name of the folder with templates
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/XC/FacebookMarketing/form_field/label';
    }

    /**
     * Return field template
     *
     * @return string
     */
    protected function getFieldTemplate()
    {
        return 'send.twig';
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

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/XC/FacebookMarketing/form_field/label/send.css';

        return $list;
    }
}