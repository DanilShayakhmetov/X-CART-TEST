<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XPay\XPaymentsCloud\Core;

/**
 * Translation core routine
 */
class Translation extends \XLite\Core\Translation implements \XLite\Base\IDecorator
{
    /**
     * Plural constants
     */
    const PLURAL_CHECK = '{{tp|';
    const PLURAL_REGEXP = '/{{tc\|(?P<choices>.+?)(?!}})\|(?P<number>[^|}]+)}}/';

    /**
     * Ordinal constants
     */
    const ORDINAL_CHECK = '{{to|';
    const ORDINAL_REGEXP = '/{{to\|(?P<choices>.+?)(?!}})\|(?P<number>[^|}]+)}}/';

    /**
     * Choose $forms[0] for singular $forms[1] for plural base on $number
     *
     * @param integer $number Number
     * @param array   $forms  Forms
     * @param string  $code   Language code
     *
     * @return string
     */
    protected static function selectPlural($number, $forms, $code)
    {
        $result = '';

        if ('ru' == $code && 3 == count($forms)) {
            $index = (($number % 10 === 1) && ($number % 100 !== 11))
                ? 0
                : (
                ($number % 10 >= 2) && ($number % 10 <= 4)
                && (($number % 100 < 10) || ($number % 100 >= 20))
                    ? 1 : 2
                );

            $result = $forms[$index];

        } elseif (2 == count($forms)) {
            $result = $forms[1 == $number ? 0 : 1];
        }

        return $result;
    }

    /**
     * Choose ordinal ending from $forms base on $number
     *
     * @param integer $number Number
     * @param array   $forms  Forms
     * @param string  $code   Language code
     *
     * @return string
     */
    protected static function selectOrdinal($number, $forms, $code)
    {
        $result = '';

        if ('ru' == $code && 3 == count($forms)) {
            // number ends on 11, 12 or 13
            if ($number % 100 > 10 && $number % 100 < 20) {
                $result = $forms[0];
            } else {
                switch ($number % 10) {
                    case 0:
                    case 1:
                    case 4:
                    case 5:
                    case 9:
                        $result = $forms[0];
                        break;

                    case 2:
                    case 6:
                    case 7:
                    case 8:
                    default:
                        $result = $forms[1];
                        break;

                    case 3:
                        $result = $forms[2];
                        break;
                }
            }

        } elseif (4 == count($forms)) {
            // number ends on 11, 12 or 13
            if ($number % 100 > 10 && $number % 100 < 14) {
                $result = $forms[3];
            } else {
                switch ($number % 10) {
                    case 1:
                        $result = $forms[0];
                        break;

                    case 2:
                        $result = $forms[1];
                        break;

                    case 3:
                        $result = $forms[2];
                        break;

                    default:
                        $result = $forms[3];
                }
            }
        }

        return $result;
    }

    /**
     * Process choices
     *
     * @param string $checkString  Check string
     * @param string $regexpString Check string
     * @param string $string       Translated string
     * @param array  $arguments    Substitute arguments OPTIONAL
     * @param string $code         Language code OPTIONAL
     *
     * @return string
     */
    protected static function processChoices(
        $checkString,
        $regexpString,
        $string,
        array $arguments = array(),
        $code = null
    ) {
        if (strpos($string, $checkString) !== false) {
            $matches = array();
            if (preg_match_all($regexpString, $string, $matches, \PREG_SET_ORDER)) {
                foreach ($matches as $match) {
                    $choice = '';
                    if (isset($arguments[$match['number']])) {
                        $forms = explode('|', $match['choices']);
                        $choice = static::selectOrdinal($arguments[$match['number']], $forms, $code);
                    }

                    if ($choice) {
                        $string = str_replace($match[0], $choice, $string);
                    }
                }
            }
        }

        return $string;
    }

    /**
     * Process plural
     *
     * @param string $string    Translated string
     * @param array  $arguments Substitute arguments OPTIONAL
     * @param string $code      Language code OPTIONAL
     *
     * @return string
     */
    protected static function processPlural($string, array $arguments = array(), $code = null)
    {
        return static::processChoices(static::PLURAL_CHECK, static::PLURAL_REGEXP, $string, $arguments, $code);
    }

    /**
     * Process ordinal
     *
     * @param string $string    Translated string
     * @param array  $arguments Substitute arguments OPTIONAL
     * @param string $code      Language code OPTIONAL
     *
     * @return string
     */
    protected static function processOrdinal($string, array $arguments = array(), $code = null)
    {
        return static::processChoices(static::ORDINAL_CHECK, static::ORDINAL_REGEXP, $string, $arguments, $code);
    }

    /**
     * Translate plural
     *
     * @param string  $name      Label name
     * @param integer $number    Number
     * @param array   $arguments Substitute arguments OPTIONAL
     * @param string  $code      Language code OPTIONAL
     *
     * @return string
     */
    public function translatePlural($name, $number, array $arguments = array(), $code = null)
    {
        $translated = $this->translate($name, $arguments, $code);
        $forms = explode('|', $translated);

        if (empty($code)) {
            $code = \XLite\Logic\Export\Generator::getLanguageCode()
                ?: \XLite\Core\Session::getInstance()->getLanguage()->getCode();
        }

        return static::selectPlural($number, $forms, $code);
    }

    /**
     * Translate ordinal
     *
     * @param string  $name      Label name
     * @param integer $number    Number
     * @param array   $arguments Substitute arguments OPTIONAL
     * @param string  $code      Language code OPTIONAL
     *
     * @return string
     */
    public function translateOrdinal($name, $number, array $arguments = array(), $code = null)
    {
        $translated = $this->translate($name, $arguments, $code);
        $forms = explode('|', $translated);

        if (empty($code)) {
            $code = \XLite\Logic\Export\Generator::getLanguageCode()
                ?: \XLite\Core\Session::getInstance()->getLanguage()->getCode();
        }

        return static::selectOrdinal($number, $forms, $code);
    }

    /**
     * Translate by string
     *
     * @param string $name      Label name
     * @param array  $arguments Substitute arguments OPTIONAL
     * @param string $code      Language code OPTIONAL
     *
     * @return string
     */
    public function translateByString($name, array $arguments = array(), $code = null)
    {
        $result = parent::translateByString($name, $arguments, $code);

        if (empty($code)) {
            $code = \XLite\Logic\Export\Generator::getLanguageCode()
                ?: \XLite\Core\Session::getInstance()->getLanguage()->getCode();
        }

        $result = $this->processPlural($result, $arguments, $code);
        $result = $this->processOrdinal($result, $arguments, $code);

        return $result;
    }

}
