<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core;

/**
 * Config cell class
 */
class ConfigCell extends \XLite\Core\CommonCell
{
    /**
     * @var bool
     */
    protected $isMain = false;

    public function __construct($isMain = false, array $data = null)
    {
        parent::__construct($data);

        $this->isMain = $isMain;
    }

    /**
     * Get property by name
     *
     * @param string $name property name
     *
     * @return mixed
     */
    public function __get($name)
    {
        $result = parent::__get($name);

        if (!$result && $this->isMain) {
            $result = new \XLite\Core\ConfigCell();
        }

        return $result;
    }
}
