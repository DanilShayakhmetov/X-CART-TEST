<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Translation;

use XLite\Core\Translation;

class Label implements \XLite\Core\Serialization\SerializableNative, \JsonSerializable
{
    use \XLite\Core\Cache\ExecuteCachedTrait;

    /**
     * @var string
     */
    private $label;

    /**
     * @var array
     */
    private $parameters;

    /**
     * @var string
     */
    private $code;

    /**
     * @var string
     */
    private $type;

    /**
     * Label constructor.
     *
     * @param string $label
     * @param array  $parameters
     * @param string $code
     * @param string $type Label type, can be used in \XLite\Core\ITranslationProcessor
     */
    public function __construct($label, $parameters = [], $code = null, $type = null)
    {
        $this->label      = $label;
        $this->parameters = $parameters;
        $this->code       = $code;
        $this->type       = $type;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->translate();
    }

    /**
     * @return string
     */
    public function translate()
    {
        return $this->executeCachedRuntime(function () {
            $r = Translation::getInstance()->translate(
                $this->label,
                $this->parameters,
                $this->code,
                $this->type
            );

            return $r;
        });
    }

    /**
     * @return string
     */
    public function jsonSerialize()
    {
        return (string) $this;
    }
}