<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select;

/**
 * Address type selector
 */
class AddressType extends \XLite\View\FormField\Select\Regular
{
    const TYPE_RESIDENTIAL = 'R';
    const TYPE_COMMERCIAL  = 'C';
    const PARAM_SELECT_ONE         = 'selectOne';
    const PARAM_SELECT_ONE_LABEL   = 'selectOneLabel';
    const PARAM_DENY_SINGLE_OPTION = 'denySingleOption';

    /**
     * Get default options list
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return array(
            static::TYPE_RESIDENTIAL => static::t('Residential'),
            static::TYPE_COMMERCIAL  => static::t('Commercial'),
        );
    }

    /**
     * getOptions
     *
     * @return array
     */
    protected function getOptions()
    {
        return $this->getParam(static::PARAM_SELECT_ONE) && (
            count(parent::getOptions()) > 1
            || !$this->isSingleOptionAllowed()
        )
            ? ['' => $this->getParam(static::PARAM_SELECT_ONE_LABEL)] + parent::getOptions()
            : parent::getOptions();
    }

    /**
     * Define widget parameters
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_SELECT_ONE         => new \XLite\Model\WidgetParam\TypeBool('Select one', true),
            static::PARAM_SELECT_ONE_LABEL   => new \XLite\Model\WidgetParam\TypeString('Select one label', $this->getDefaultSelectOneLabel()),
            static::PARAM_DENY_SINGLE_OPTION => new \XLite\Model\WidgetParam\TypeBool('Deny single option', false),
        ];
    }

    /**
     * @inheritdoc
     */
    protected function isSingleOptionAllowed()
    {
        return !$this->getParam(static::PARAM_DENY_SINGLE_OPTION);
    }

    /**
     * Default 'Select one' label
     *
     * @return string
     */
    protected function getDefaultSelectOneLabel()
    {
        return static::t('Select type');
    }
}
