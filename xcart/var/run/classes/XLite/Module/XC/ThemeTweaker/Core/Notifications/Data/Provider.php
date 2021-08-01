<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\Core\Notifications\Data;


abstract class Provider
{
    /**
     * @param string $templateDir
     *
     * @return mixed
     */
    abstract public function getData($templateDir);

    /**
     * @param string $templateDir
     *
     * @return string
     */
    abstract public function getName($templateDir);

    /**
     * @param string $templateDir
     *
     * @param mixed  $value
     *
     * @return array
     */
    abstract public function validate($templateDir, $value);

    /**
     * @param string $templateDir
     *
     * @return boolean
     */
    abstract public function isAvailable($templateDir);

    /**
     * @param string $templateDir
     *
     * @return array
     */
    public function getSuitabilityErrors($templateDir)
    {
        return [];
    }

    /**
     * @return array
     */
    abstract protected function getTemplateDirectories();

    /**
     * @param string $templateDir
     *
     * @return boolean
     */
    public function isApplicable($templateDir)
    {
        return in_array(
            $templateDir,
            $this->getTemplateDirectories(),
            true
        );
    }

    /**
     * @param string $templateDir
     *
     * @return string
     */
    public function buildKey($templateDir)
    {
        return md5(get_class($this) . '|' . $templateDir);
    }

    /**
     * @param string $templateDir
     *
     * @return mixed
     */
    public function getValue($templateDir)
    {
        return \XLite\Core\TmpVars::getInstance()->{$this->buildKey($templateDir)};
    }

    /**
     * @param string $templateDir
     * @param mixed  $value
     *
     * @return $this
     */
    public function setValue($templateDir, $value)
    {
        \XLite\Core\TmpVars::getInstance()->{$this->buildKey($templateDir)} = $value;
        return $this;
    }
}