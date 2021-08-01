<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\Core\Notifications\Data;


/**
 * Constant
 */
class Constant extends Provider
{
    private $name;
    private $data;
    private $directory;

    public function __construct($name, $data, $directory)
    {
        $this->name = $name;
        $this->data = $data;
        $this->directory = $directory;
    }

    public function getData($templateDir)
    {
        return $this->data;
    }

    public function getName($templateDir)
    {
        return $this->name;
    }

    public function validate($templateDir, $value)
    {
        return [];
    }

    public function isAvailable($templateDir)
    {
        return true;
    }

    protected function getTemplateDirectories()
    {
        return [
            $this->directory
        ];
    }
}