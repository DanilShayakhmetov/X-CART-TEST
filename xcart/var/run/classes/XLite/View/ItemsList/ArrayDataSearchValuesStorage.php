<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ItemsList;


class ArrayDataSearchValuesStorage extends \XLite\View\ItemsList\ASearchValuesStorage
{
    /**
     * Request data
     */
    protected $data;

    /**
     * @param array    $data Widget params data array
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Get param value
     *
     * @param $name
     *
     * @return mixed
     */
    protected function getInnerValue($name)
    {
        if (!isset($this->data[$name])) {
            return null;
        }

        return $this->data[$name];
    }

    /**
     * Update inner storage
     */
    protected function updateInner()
    {
    }

    /**
     * Get param value
     *
     * @param string $name Search condition service name
     * @param mixed  $value
     */
    public function setValue($name, $value)
    {
        $this->data[$name] = $value;
    }
}
