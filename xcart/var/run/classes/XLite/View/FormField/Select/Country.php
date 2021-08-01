<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select;

use XLite\Core\Cache\ExecuteCachedTrait;
use XLite\Core\PreloadedLabels\ProviderInterface;

/**
 * \XLite\View\FormField\Select\Country
 */
class Country extends \XLite\View\FormField\Select\Regular implements ProviderInterface
{
    use ExecuteCachedTrait;
    use SingleOptionAsLabelTrait;

    /**
     * Widget param names
     */
    const PARAM_ALL                = 'all';
    const PARAM_STATE_SELECTOR_ID  = 'stateSelectorId';
    const PARAM_STATE_INPUT_ID     = 'stateInputId';
    const PARAM_SELECT_ONE         = 'selectOne';
    const PARAM_SELECT_ONE_LABEL   = 'selectOneLabel';
    const PARAM_DENY_SINGLE_OPTION = 'denySingleOption';

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

        parent::__construct($params);
    }

    /**
     * @inheritdoc
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = $this->getDir() . '/select_country.js';

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
            static::PARAM_ALL                => new \XLite\Model\WidgetParam\TypeBool('All', false),
            static::PARAM_STATE_SELECTOR_ID  => new \XLite\Model\WidgetParam\TypeString('State select ID', null),
            static::PARAM_STATE_INPUT_ID     => new \XLite\Model\WidgetParam\TypeString('State input ID', null),
            static::PARAM_SELECT_ONE         => new \XLite\Model\WidgetParam\TypeBool('All', true),
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
        return static::t('Select one');
    }

    /**
     * Get selector default options list
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        $options = [];

        foreach ($this->getCountries() as $country) {
            $options[$country->getCode()] = $country->getCountry();
        }

        return $options;
    }

    /**
     * @return mixed
     */
    protected function getCountries()
    {
        return $this->executeCachedRuntime(function () {
            return $this->onlyEnabled
                ? \XLite\Core\Database::getRepo('XLite\Model\Country')->findAllEnabled()
                : \XLite\Core\Database::getRepo('XLite\Model\Country')->findAllCountries();
        }, [__METHOD__]);
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
     * getDefaultValue
     *
     * @return string
     */
    protected function getDefaultValue()
    {
        $country = \XLite\Model\Address::getDefaultFieldValue('country');

        return $country
            ? $country->getCode()
            : '';
    }

    /**
     * @return array
     */
    protected function getStateAutocompleteCountries()
    {
        $countries = \Includes\Utils\ConfigParser::getOptions([
            'storefront_options',
            'autocomplete_states_for_countries'
        ]);

        $countries = array_filter(array_map('trim', explode(',', $countries)));

        return $countries;
    }

    /**
     * Return some data for JS external scripts if it is needed.
     *
     * @return null|array
     */
    protected function getFormFieldJSData()
    {
        return [
            'statesList'       => \XLite\Core\Database::getRepo('XLite\Model\Country')->findCountriesStatesGrouped(),
            'forceCustomState' => $this->getStateAutocompleteCountries(),
            'stateSelectors'   => [
                'fieldId'         => $this->getFieldId(),
                'stateSelectorId' => $this->getParam(static::PARAM_STATE_SELECTOR_ID),
                'stateInputId'    => $this->getParam(static::PARAM_STATE_INPUT_ID),
            ],
        ];
    }

    /**
     * Get value container class
     *
     * @return string
     */
    protected function getValueContainerClass()
    {
        return parent::getValueContainerClass() . ' country-selector';
    }

    /**
     * Array of labels in following format.
     *
     * 'label' => 'translation'
     *
     * @return mixed
     */
    public function getPreloadedLanguageLabels()
    {
        return [
            'Select one' => static::t('Select one')
        ];
    }
}
