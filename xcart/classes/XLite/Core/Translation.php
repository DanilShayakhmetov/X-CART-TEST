<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core;

use Includes\Utils\FileManager;

/**
 * Translation core rutine
 */
class Translation extends \XLite\Base\Singleton implements \XLite\Base\IREST
{
    /**
     * Default language
     */
    const DEFAULT_LANGUAGE = 'en';

    /**
     * Translation driver
     *
     * @var \XLite\Core\TranslationDriver\ATranslationDriver
     */
    protected $driver;

    /**
     * Translation postprocessor
     *
     * @var \XLite\Core\ITranslationProcessor
     */
    protected $processor;

    /**
     * Translation drivers query
     *
     * @var \XLite\Core\TranslationDriver\ATranslationDriver[]
     */
    protected $driversQuery = array(
        '\XLite\Core\TranslationDriver\Gettext',
        '\XLite\Core\TranslationDriver\Db',
        '\XLite\Core\TranslationDriver\DbDirect',
    );

    /**
     * Temporary translation language (used in pdfs and emails)
     */
    protected static $tmpTranslationCode = '';

    /**
     * Get translation (short static method)
     * TODO: to remove
     *
     * @param string $name      Label name
     * @param array  $arguments Substitute arguments OPTIONAL
     * @param string $code      Language code OPTIONAL
     * @param string $type      Label type, can be used in \XLite\Core\ITranslationProcessor
     *
     * @return string
     */
    public static function lbl($name, array $arguments = array(), $code = null, $type = null)
    {
        return $name instanceof \XLite\Core\Translation\Label
            ? $name
            : new \XLite\Core\Translation\Label($name, $arguments, $code, $type);
    }

    /**
     * Get language query
     *
     * @param string $code Specified code OPTIONAL
     *
     * @return array
     */
    public static function getLanguageQuery($code = null)
    {
        $list = array(
            $code ?: \XLite\Core\Session::getInstance()->getLanguage()->getCode(),
            static::getDefaultLanguage(),
            static::DEFAULT_LANGUAGE
        );

        return array_unique($list);
    }

    /**
     * Reset driver
     *
     * @return void
     */
    public function resetDriver()
    {
        $this->driver = null;
    }

    /**
     * Reset driver cache
     *
     * @return void
     */
    public function reset()
    {
        $this->getDriver()->reset();
    }

    /**
     * Get translation
     *
     * @param string $name      Label name
     * @param array  $arguments Substitute arguments OPTIONAL
     * @param string $code      Language code OPTIONAL
     * @param string $type      Label type, can be used in \XLite\Core\ITranslationProcessor
     *
     * @return string
     */
    public function translate($name, array $arguments = array(), $code = null, $type = null)
    {
        $result = '';

        if ($name instanceof \XLite\Core\Translation\Label) {
            return (string)$name;
        }

        if (!empty($name)) {
            if (empty($code)) {
                $code = $this->getTranslationLanguageCode();
            }

            $handler = $this->getLabelHandler($name, $code);

            $result = $handler
                ? $this->translateByHandler($handler, $name, $arguments, $code)
                : $this->translateByString($name, $arguments, $code);

            $result = $this->postprocessTranslation($result, $name, $arguments, $code, $type);
        }

        return $result;
    }

    /**
     * Get necessary language code for using in 'translate()' method
     *
     * @return string
     */
    protected function getTranslationLanguageCode()
    {
        $code = \XLite\Logic\Export\Generator::getLanguageCode();

        if (!$code) {
            $code = $this->getTmpTranslationCode()
                ?: \XLite\Core\Session::getInstance()->getLanguage()->getCode();
        }

        return $code;
    }

    /**
     * Get tmpTranslationCode
     *
     * @return string
     */
    public static function getTmpTranslationCode()
    {
        return static::$tmpTranslationCode;
    }

    /**
     * Set tmpTranslationCode
     *
     * @param string $code Code
     *
     * @return string
     */
    public static function setTmpTranslationCode($code = '')
    {
        static::$tmpTranslationCode = $code;
    }

    /**
     * Get REST entity names
     *
     * @return array
     */
    public function getRESTNames()
    {
        return array (
            'translation',
        );
    }

    /**
     * Get translation as REST
     *
     * @param string $id        Label name
     * @param array  $arguments Arguments
     *
     * @return string
     */
    public function getTranslationREST($id, $arguments)
    {
        if (!is_array($arguments) || !$arguments) {
            $arguments = array();
        }

        return (string) $this->translate($id, $arguments);
    }

    /**
     * Get translation driver
     *
     * @return \XLite\Core\TranslationDriver\ATranslationDriver
     */
    public function getDriver()
    {
        if (!isset($this->driver)) {
            $this->driver = $this->defineDriver();
        }

        return $this->driver;
    }

    /**
     * Get translation postprocessor.
     *
     * @return ITranslationProcessor
     */
    public function getProcessor()
    {
        return $this->processor;
    }

    /**
     * Set translation postprocessor.
     *
     * @param ITranslationProcessor $value
     * @return $this
     */
    public function setProcessor(ITranslationProcessor $value = null)
    {
        $this->processor = $value;
        return $this;
    }

    /**
     * Get translation language
     *
     * @param string $code Language code
     *
     * @return \XLite\Core\TranslationLanguage\ATranslationLanguage
     */
    protected function getTranslationLanguage($code)
    {
        if (!isset($this->translationLanguages[$code])) {
            $classes = $this->defineTranslationLanguages();
            $class = empty($classes[$code])
                ? '\\XLite\\Core\\TranslationLanguage\\Common'
                : $classes[$code];

            $this->translationLanguages[$code] = new $class();
        }

        return $this->translationLanguages[$code];
    }

    /**
     * Define translation languages
     *
     * @return array
     */
    protected function defineTranslationLanguages()
    {
        return array(
            'en' => '\\XLite\\Core\\TranslationLanguage\\English',
        );
    }

    /**
     * Get label handler
     *
     * @param string $name Label name
     * @param string $code Language code
     *
     * @return string
     */
    protected function getLabelHandler($name, $code)
    {
        $language = $this->getTranslationLanguage($code);

        return $language
            ? $language->getLabelHandler($name)
            : null;
    }

    /**
     * Translate by handler
     *
     * @param callable $handler   Handler
     * @param string   $name      Label name
     * @param array    $arguments Substitute arguments
     * @param string   $code      Language code
     *
     * @return string
     */
    protected function translateByHandler($handler, $name, array $arguments, $code)
    {
        return call_user_func($handler, $arguments, $code, $name);
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
        if (empty($code)) {
            $code = \XLite\Logic\Export\Generator::getLanguageCode()
                ?: \XLite\Core\Session::getInstance()->getLanguage()->getCode();
        }

        try {
            $result = $this->getDriver()->translate($name, $code);
        } catch (\XLite\Core\Exception\TranslationDriverError $e) {
            \XLite\Logger::getInstance()->log($e->getMessage(), LOG_NOTICE);
            $this->resetDriver();
            return $this->translateByString($name, $arguments, $code);
        }


        if (!isset($result)) {
            $result = $name;
        }

        if (!empty($arguments)) {
            $result = $this->processSubstitute($result, $arguments);
        }

        return $result;
    }

    /**
     * Translate by string
     *
     * @param string $translation Translated label string
     * @param string $name        Label name
     * @param array  $arguments   Substitute arguments OPTIONAL
     * @param string $code        Language code OPTIONAL
     * @param string $type        Label type, can be used in \XLite\Core\ITranslationProcessor
     *
     * @return string
     */
    public function postprocessTranslation($translation, $name, array $arguments = [], $code = null, $type = null)
    {
        if (empty($code)) {
            $code = \XLite\Logic\Export\Generator::getLanguageCode()
                ?: \XLite\Core\Session::getInstance()->getLanguage()->getCode();
        }

        if ($this->getProcessor()) {
            $translation = $this->getProcessor()->postprocess($translation, $name, $arguments, $code, $type);
        }

        return $translation;
    }

    /**
     * Process substitute
     *
     * @param string $string Translated label
     * @param array  $args   Substitute arguments
     *
     * @return string
     */
    protected function processSubstitute($string, array $args)
    {
        $keys = array();
        $values = array();
        foreach ($args as $k => $v) {
            $keys[] = '{{' . $k . '}}';
            $values[$k] = $v;
        }

        return $this->getProcessor()
            ? $this->getProcessor()->replaceVariables($string, $keys, $values)
            : str_replace($keys, $values, $string);
    }

    /**
     * Define translation driver
     *
     * @return \XLite\Core\TranslationDriver\ATranslationDriver
     */
    protected function defineDriver()
    {
        $driver = null;

        $translationDriver = \XLite::getInstance()->getOptions(array('other', 'translation_driver'));
        if (defined('LC_CACHE_BUILDING') && !defined('LC_CACHE_BUILDING_FINISH')) {
            $translationDriver = 'DbDirect';
        }

        if ($translationDriver && 'auto' != $translationDriver) {
            $class = '\XLite\Core\TranslationDriver\\'
                . \XLite\Core\Converter::convertToCamelCase($translationDriver);
            if (in_array($class, $this->driversQuery)) {
                $driver = new $class();
                if (!$driver->isValid()) {
                    $driver = null;
                }
            }
        }

        if (!$driver) {
            foreach ($this->driversQuery as $class) {
                $driver = $class::getInstance();
                if ($driver->isValid()) {
                    break;
                }
                $driver = null;
            }

            if (!isset($driver)) {
                // TODO - add throw exception
                $this->doDie('Unable to find a translation driver!');
            }
        }

        return $driver;
    }

    // {{{ Formatters

    /**
     * Format time period
     *
     * @param integer $seconds Time period (seconds)
     * @param string  $code    Language code OPTIONAL
     *
     * @return string
     */
    public static function formatTimePeriod($seconds, $code = null)
    {
        $seconds = abs(round($seconds));

        $h = intval(gmdate('H', $seconds));
        $m = intval(gmdate('i', $seconds));
        $s = intval(gmdate('s', $seconds));

        $result = array();

        if (0 < $h) {
            $result[] = static::lbl('X hours', array('hours' => $h), $code);
        }

        if (0 < $m) {
            $result[] = static::lbl('X minutes', array('minutes' => $m), $code);
        }

        if (0 < $s) {
            $result[] = static::lbl('X seconds', array('seconds' => $s), $code);
        }

        return implode(' ', $result);
    }

    /**
     * Translate weight symbol
     *
     * @return string
     */
    public static function translateWeightSymbol()
    {
        return \XLite\Core\Config::getInstance()->Units->weight_symbol
            ? static::lbl(\XLite\Core\Config::getInstance()->Units->weight_symbol)
            : '';
    }

    /**
     * Translate dim symbol
     *
     * @return string
     */
    public static function translateDimSymbol()
    {
        return \XLite\Core\Config::getInstance()->Units->dim_symbol
            ? static::lbl(\XLite\Core\Config::getInstance()->Units->dim_symbol)
            : '';
    }

    // }}}

    // {{{ Load labels from YAML file

    /**
     * Intellectual loading of language labels from yaml file (for usage mainly in upgrade hooks):
     * Do not insert/update labels which are already exist in database.
     * Note: Source Yaml file must contain labels with single translation only
     * Method returns true if labels were uploaded to the database
     *
     * @param string $fileName File name
     *
     * @return boolean
     */
    public static function loadLabelsFromYaml($fileName)
    {
        $result = false;

        $data = FileManager::parseYamlFile($fileName);

        $dbLabels = array();

        if (!empty($data) && is_array($data) && !empty($data['XLite\Model\LanguageLabel'])) {

            $labels = $data['XLite\Model\LanguageLabel'];

            $rows = array();

            foreach ($labels as $label) {
                if (isset($label['name'])) {
                    $name = $label['name'];

                    if (!empty($label['translations'])) {

                        foreach ($label['translations'] as $k => $labelData) {

                            $code = $labelData['code'];

                            if (!isset($dbLabels[$code])) {
                                $dbLabels[$code] = \XLite\Core\Database::getRepo('XLite\Model\LanguageLabel')->findLabelsTranslatedToCode($code);
                            }

                            if (isset($dbLabels[$code][$name])) {
                                unset($label['translations'][$k]);
                            }
                        }
                    }

                    if (!empty($label['translations'])) {
                        $rows[] = $label;
                    }
                }
            }

            if ($rows) {
                // Load labels into DB
                \XLite\Core\Database::getRepo('XLite\Model\LanguageLabel')->loadFixtures($rows);
                \XLite\Core\Database::getEM()->flush();
                \XLite\Core\Database::getEM()->clear();

                $result = true;
            }
        }

        return $result;
    }

    // }}}
}
