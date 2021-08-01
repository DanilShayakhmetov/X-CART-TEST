<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Input\Text;

/**
 * Date
 */
class Date extends \XLite\View\FormField\Input\Text
{
    /**
     * Widget param names
     */
    const PARAM_MIN       = 'min';
    const PARAM_MAX       = 'max';
    const PARAM_HIGH_YEAR = 'highYear';
    const PARAM_LOW_YEAR  = 'lowYear';

    /**
     * Register files from common repository
     *
     * @return array
     */
    public function getCommonFiles()
    {
        $list                        = parent::getCommonFiles();
        $list[static::RESOURCE_JS][] = 'js/jquery-ui-i18n.min.js';
        $list[static::RESOURCE_JS][] = 'form_field/js/date.js';
        $list[static::RESOURCE_CSS][] = 'form_field/css/date.less';

        return $list;
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = $this->getDir() . '/input/text/date.less';

        return $list;
    }

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            self::PARAM_MIN       => new \XLite\Model\WidgetParam\TypeInt('Minimum date', null),
            self::PARAM_MAX       => new \XLite\Model\WidgetParam\TypeInt('Maximum date', null),
            self::PARAM_HIGH_YEAR => new \XLite\Model\WidgetParam\TypeInt('The high year', 2035),
            self::PARAM_LOW_YEAR  => new \XLite\Model\WidgetParam\TypeInt('The low year', date('Y', \XLite\Core\Converter::time()) - 1),
        ];
    }

    /**
     * Check field validity
     *
     * @return boolean
     */
    protected function checkFieldValidity()
    {
        $result = parent::checkFieldValidity();

        if ($result) {
            $result = $this->checkRange();
        }

        return $result;
    }

    /**
     * Check range
     *
     * @return boolean
     */
    protected function checkRange()
    {
        $result = true;

        if (!is_null($this->getParam(self::PARAM_MIN)) && $this->getValue() < $this->getParam(self::PARAM_MIN)) {

            $result             = false;
            $this->errorMessage = \XLite\Core\Translation::lbl(
                'The value of the X field must be greater than Y',
                [
                    'name' => $this->getLabel(),
                    'min'  => $this->formatDate($this->getParam(self::PARAM_MIN)),
                ]
            );

        } elseif (!is_null($this->getParam(self::PARAM_MAX)) && $this->getValue() > $this->getParam(self::PARAM_MAX)) {

            $result             = false;
            $this->errorMessage = \XLite\Core\Translation::lbl(
                'The value of the X field must be less than Y',
                [
                    'name' => $this->getLabel(),
                    'max'  => $this->formatDate($this->getParam(self::PARAM_MAX)),
                ]
            );

        }

        return $result;
    }

    /**
     * Sanitize value
     *
     * @return integer
     */
    protected function sanitize()
    {
        return parent::sanitize() ?: 0;
    }

    /**
     * Set value
     *
     * @param mixed $value Value to set
     *
     * @return void
     */
    public function setValue($value)
    {
        if (!is_numeric($value)) {
            $value = \XLite\Core\Converter::parseFromJsFormat($value);
        }

        parent::setValue($value);
    }

    public function getValueAsString()
    {
        $value = parent::getValue();

        $result = '';

        if (0 < (int) $value) {

            $formats = \XLite\Core\Converter::getDateFormatsByStrftimeFormat(
                \XLite\Core\Config::getInstance()->Units->date_format
            );
            $format  = $formats['phpFormat'];

            $result = date($format, $value);
        }

        return $result;
    }

    /**
     * getCommonAttributes
     *
     * @return array
     */
    protected function getCommonAttributes()
    {
        $list = parent::getCommonAttributes();

        if (is_numeric($list['value']) || is_int($list['value'])) {
            $list['value'] = $this->getValueAsString();
        }

        return $list;
    }

    /**
     * @return int
     */
    protected function getStartDay()
    {
        $start = \XLite\Core\Config::getInstance()->Units->week_start;

        $starts = [
            'sun' => 0,
            'mon' => 1,
            'tue' => 2,
            'wed' => 3,
            'thu' => 4,
            'fri' => 5,
            'sat' => 6,
        ];

        return isset($starts[$start])
            ? $starts[$start]
            : 0;
    }

    /**
     * Register some data that will be sent to template as special HTML comment
     *
     * @return array
     */
    protected function getCommentedData()
    {
        $data = parent::getCommentedData();

        $currentFormats     = \XLite\Core\Converter::getDateFormatsByStrftimeFormat();
        $data['dateFormat'] = $currentFormats['jsFormat'];
        $data['firstDay']   = $this->getStartDay();
        $data['locale']     = $this->getLocaleCode(\XLite\Core\Session::getInstance()->getLanguage()->getCode());
        $data['highYear']   = $this->getParam(static::PARAM_HIGH_YEAR);
        $data['lowYear']    = $this->getParam(static::PARAM_LOW_YEAR);

        return $data;
    }

    /**
     * @param string $language
     *
     * @return string
     */
    protected function getLocaleCode($language)
    {
        $locales = [
            'zh_CN',
        ];

        $locale = array_filter($locales, function ($item) use ($language) {
            return strpos($item, strtolower($language)) === 0;
        });

        return 1 === count($locale) ? reset($locale) : $language;
    }

    /**
     * Assemble classes
     *
     * @param array $classes Classes
     *
     * @return array
     */
    protected function assembleClasses(array $classes)
    {
        $classes = parent::assembleClasses($classes);

        $classes[] = 'datepicker';

        return $classes;
    }

    /**
     * Get default maximum size
     *
     * @return integer
     */
    protected function getDefaultMaxSize()
    {
        return 50;
    }

    /**
     * Return field template
     *
     * @return string
     */
    protected function getFieldTemplate()
    {
        return 'date.twig';
    }

    /**
     * Get default placeholder
     *
     * @return string
     */
    protected function getDefaultPlaceholder()
    {
        return static::t('Click to select the date');
    }
}
