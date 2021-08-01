<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\Core;

use XLite\Core\ITranslationProcessor;

class EditableTranslationProcessor implements ITranslationProcessor
{

    protected $requestLabels = [];

    /**
     * Returns all registered labels of this request
     *
     * @return array
     */
    public function getRegisteredLabels()
    {
        return $this->requestLabels;
    }

    /**
     * Performs postprocessing on the given translation string
     *
     * @param        $translation
     * @param        $name
     * @param        $arguments
     * @param        $code
     * @param string $type Label type, can be used in \XLite\Core\ITranslationProcessor
     *
     * @return string
     */
    public function postprocess($translation, $name, $arguments, $code, $type)
    {
        if (!$this->isEditable($translation, $name, $arguments, $code, $type)) {
            return $translation;
        }

        $label = "<span class='xlite-translation-label disabled' data-xlite-label-name='$name' data-xlite-label-code='$code'>$translation</span>";

        $result = new \Twig_Markup($label, 'UTF-8');

        $this->registerDisplayedLabel($label, $name, $arguments, $code);

        return $result;
    }

    /**
     * Performs variable replacement on string with keys of {{var}} format
     *
     * @param $string
     * @param $keys
     * @param $values
     *
     * @return string
     */
    public function replaceVariables($string, $keys, $values)
    {
        foreach ($values as $key => $value) {
            $values[$key] = "<span class='xlite-translation-var' data-xlite-var-key='$key'>$value</span>";
        }

        return str_replace($keys, $values, $string);
    }

    /**
     * Register label to runtime storage of currently displayed labels.
     *
     * @param $translation
     * @param $name
     * @param $arguments
     * @param $code
     */
    protected function registerDisplayedLabel($translation, $name, $arguments, $code)
    {
        $this->requestLabels[$name] = [
            'translation' => (string) $translation,
            'name'        => (string) $name,
            'arguments'   => $arguments,
            'code'        => $code,
        ];
    }

    /**
     * @param        $translation
     * @param        $name
     * @param        $arguments
     * @param        $code
     * @param string $type Label type, can be used in \XLite\Core\ITranslationProcessor
     *
     * @return bool
     */
    protected function isEditable($translation, $name, $arguments, $code, $type)
    {
        return !in_array($type, $this->getNonEditableTypes(), true);
    }

    /**
     * @return array
     */
    protected function getNonEditableTypes()
    {
        return ['placeholder'];
    }
}