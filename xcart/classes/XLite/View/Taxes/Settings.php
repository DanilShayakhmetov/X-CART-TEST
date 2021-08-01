<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Taxes;

/**
 * Zone selector
 */
abstract class Settings extends \XLite\View\AView
{
    /**
     * @return array
     */
    public function getCSSFiles()
    {
        return [
            'tax_settings/style.less'
        ];
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'tax_settings/page.twig';
    }

    /**
     * @return string
     */
    protected function getSettingsTemplate()
    {
        return 'tax_settings/settings.twig';
    }

    /**
     * @return string
     */
    protected function getItemsTemplate()
    {
        return null;
    }

    /**
     * @return string
     */
    protected function getOptionFieldsTemplate()
    {
        return null;
    }

    /**
     * @return string
     */
    protected function getFormClass()
    {
        return 'XLite\View\Form\SimpleForm';
    }

    /**
     * @return array
     */
    protected function getFormWidgetParams()
    {
        return [
            'formTarget' => $this->getFormTarget(),
            'formAction' => 'update'
        ];
    }

    /**
     * @return string
     */
    abstract protected function getFormTarget();

    /**
     * Get CSS classes for dialog block
     *
     * @return string
     */
    protected function getDialogCSSClasses()
    {
        return 'edit-tax-settings';
    }

    /**
     * Return true if common tax settings should be displayed as expanded section
     *
     * @return boolean
     */
    protected function isCommonOptionsExpanded()
    {
        return true;
    }

    /**
     * Show tax title field
     *
     * @return bool
     */
    protected function showTitleField()
    {
        return true;
    }

    /**
     * Get tax
     *
     * @return object
     */
    public function getTax()
    {
        return null;
    }

    /**
     * Return true if page has sticky panel
     *
     * @return boolean
     */
    public function hasStickyPanel()
    {
        return true;
    }

    /**
     * @return string
     */
    public function getStickyPanelClass()
    {
        return 'XLite\View\StickyPanel\ItemForm';
    }
}
