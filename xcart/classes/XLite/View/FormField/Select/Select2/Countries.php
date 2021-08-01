<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select\Select2;


class Countries extends \XLite\View\FormField\Select\Multiple
{
    /**
     * Widget param names
     */
    const PARAM_ALL               = 'all';
    const PARAM_STATE_SELECTOR_ID = 'stateSelectorId';
    const PARAM_STATE_INPUT_ID    = 'stateInputId';
    const PARAM_FULL_NAMES        = 'fullNames';

    /**
     * Show full names of countries
     *
     * @var bool
     */
    protected $fullNames = false;

    /**
     * Register files from common repository
     *
     * @return array
     */
    public function getCommonFiles()
    {
        $list = parent::getCommonFiles();
        $list[static::RESOURCE_JS][] = 'select2/dist/js/select2.min.js';
        $list[static::RESOURCE_CSS][] = 'select2/dist/css/select2.min.css';

        return $list;
    }

    /**
     * Display only enabled countries
     *
     * @var boolean
     */
    protected $onlyEnabled = true;

    /**
     * @inheritdoc
     */
    public function __construct(array $params = [])
    {
        if (!empty($params[static::PARAM_ALL])) {
            $this->onlyEnabled = false;
        }

        if (!empty($params[static::PARAM_FULL_NAMES])) {
            $this->onlyEnabled = (bool)$params[static::PARAM_FULL_NAMES];
        }

        parent::__construct($params);
    }

    /**
     * @inheritdoc
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'form_field/select/select2/countries.js';

        return $list;
    }

    /**
     * @inheritdoc
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'form_field/input/text/autocomplete.css';

        return $list;
    }

    /**
     * Pass the DOM Id fo the "States" selectbox
     * NOTE: this function is public since it's called from the View_Model_Profile_AProfile class
     *
     * @param string $selectorId DOM Id of the "States" selectbox
     * @param string $inputId    DOM Id of the "States" inputbox
     *
     * @return void
     */
    public function setStateSelectorIds($selectorId, $inputId)
    {
        $this->getWidgetParams(static::PARAM_STATE_SELECTOR_ID)->setValue($selectorId);
        $this->getWidgetParams(static::PARAM_STATE_INPUT_ID)->setValue($inputId);
    }


    /**
     * Define widget parameters
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_ALL               => new \XLite\Model\WidgetParam\TypeBool('All', false),
            static::PARAM_FULL_NAMES        => new \XLite\Model\WidgetParam\TypeBool('Full names', false),
            static::PARAM_STATE_SELECTOR_ID => new \XLite\Model\WidgetParam\TypeString('State select ID', null),
            static::PARAM_STATE_INPUT_ID    => new \XLite\Model\WidgetParam\TypeString('State input ID', null),
        ];
    }

    /**
     * Get selector default options list
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        $list = $this->onlyEnabled
            ? \XLite\Core\Database::getRepo('XLite\Model\Country')->findAllEnabled()
            : \XLite\Core\Database::getRepo('XLite\Model\Country')->findAllCountries();

        $options = [];

        foreach ($list as $country) {
            $options[$country->getCode()] = $country->getCountry();
        }

        return $options;
    }

    /**
     * Get value container class
     *
     * @return string
     */
    protected function getValueContainerClass()
    {
        return parent::getValueContainerClass() . ' input-countries-select2';
    }

    /**
     * @return array
     */
    protected function getAttributes()
    {
        $attrs =  parent::getAttributes();

        if ($this->fullNames) {
            $attrs['data-full-names'] = 'true';
        }
        return $attrs;
    }
}