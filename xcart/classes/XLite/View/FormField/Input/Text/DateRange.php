<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Input\Text;

/**
 * Date range
 */
class DateRange extends \XLite\View\FormField\Input\Text
{
    /**
     * Labels displayed
     *
     * @var   boolean
     */
    protected static $labelsDisplayed = false;

    /**
     * Return field template
     *
     * @return string
     */
    protected function getFieldTemplate()
    {
        return 'input/text/date_range.twig';
    }

    /**
     * Parse range as string
     *
     * @param string $string String
     * @param string $format Format
     *
     * @return array
     */
    public static function convertToArray($string, $format = null)
    {
        return \XLite\Core\Converter::convertRangeStringToArray($string, $format ?: static::getDateFormat(), static::getDatesSeparator());
    }

    /**
     * Get used  date format
     *
     * @param boolean $forJS Flag: return format for JS DateRangePicker script (true) or for php's date() function (false)
     *
     * @return string
     */
    protected static function getDateFormat($forJS = false)
    {
        $formats = \XLite\Core\Converter::getDateFormatsByStrftimeFormat();
        return $forJS ? $formats['jsFormat'] : $formats['phpFormat'];
    }

    /**
     * Get separate string between start date and end date
     *
     * @return string
     */
    protected static function getDatesSeparator()
    {
        return ' ~ ';
    }

    /**
     * Register files from common repository
     *
     * @return array
     */
    protected function getCommonFiles()
    {
        $list = parent::getCommonFiles();

        $list[static::RESOURCE_JS][] = 'js/jquery-ui-i18n.min.js';
        $list[static::RESOURCE_JS][] = 'js/moment.min.js';
        $list[static::RESOURCE_JS][] = 'js/jquery.comiseo.daterangepicker.js';
        $list[static::RESOURCE_CSS][] = 'css/jquery.comiseo.daterangepicker.css';

        return $list;
    }

    /**
     * Get CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'form_field/input/text/date.less';
        $list[] = 'form_field/input/text/date_range.less';

        return $list;
    }

    /**
     * Get a list of JS files required to display the widget properly
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = $this->getDir() . '/js/date_range.js';

        return $list;
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
        if (is_array($value)) {
            $value = $this->convertToString($value);
        }

        parent::setValue($value);
    }

    /**
     * Get formatted range
     *
     * @return string
     */
    protected function convertToString(array $value)
    {
        return \XLite\Core\Converter::convertArrayToRangeString(
            $value,
            static::getDateFormat(),
            static::getDatesSeparator()
        );
    }

    /**
     * Add attribute 'data-end-date' to input field
     *
     * @return array
     */
    protected function getCommonAttributes()
    {
        $result = parent::getCommonAttributes();

        $result['data-end-date'] = date(static::getDateFormat(), \XLite\Core\Converter::convertTimeToUser());
        $result['data-datarangeconfig'] = $this->getDateRangeConfig();

        return $result;
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
     * Get config settings for DateRangePicker
     *
     * @return string
     */
    protected function getDateRangeConfig()
    {
        $lng = \XLite\Core\Session::getInstance()->getLanguage()
            ? \XLite\Core\Session::getInstance()->getLanguage()->getCode()
            : 'en';

        $config = array(
            'separator' => static::getDatesSeparator(),
            'language'  => $lng,
            'startDay'  => $this->getStartDay(),
            'format'    => static::getDateFormat(true),
            'labels'    => [
                'today'           => static::t('Today'),
                'thisWeek'       => static::t('This week'),
                'thisMonth'      => static::t('This month'),
                'thisQuarter'    => static::t('This quarter'),
                'thisYear'       => static::t('This year'),
                'allTime'        => static::t('All time'),
            ]
        );

        return json_encode($config);
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
        $list = parent::assembleClasses($classes);

        $list[] = 'date-range';

        return $list;
    }

    /**
     * Get default placeholder
     *
     * @return string
     */
    protected function getDefaultPlaceholder()
    {
        return static::t('Enter date range');
    }
}
