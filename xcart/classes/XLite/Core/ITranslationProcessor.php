<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core;

interface ITranslationProcessor
{

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
    public function postprocess($translation, $name, $arguments, $code, $type);

    /**
     * Performs variable replacement on string with keys of {{var}} format
     *
     * @param $string
     * @param $keys
     * @param $values
     *
     * @return string
     */
    public function replaceVariables($string, $keys, $values);
}
