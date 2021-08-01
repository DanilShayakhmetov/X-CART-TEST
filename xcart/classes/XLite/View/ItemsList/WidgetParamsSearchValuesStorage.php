<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ItemsList;


class WidgetParamsSearchValuesStorage extends \XLite\View\ItemsList\ASearchValuesStorage
{
    /**
     * Request data
     */
    protected $data;

    /**
     * @param \XLite\Model\WidgetParam\AWidgetParam[]    $data Widget params data array
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Get param value
     *
     * @param string $serviceName Search condition service name
     *
     * @return mixed
     */
    protected function getInnerValue($serviceName)
    {
        if (!isset($this->data[$serviceName])) {
            return null;
        }

        return $this->data[$serviceName]->value;
    }

    /**
     * Update inner storage
     */
    protected function updateInner()
    {
        // No op
        // Will not be implemented, consider to separate interfaces for storing and providing-only
    }

    /**
     * Get param value
     *
     * @param string $serviceName Search condition service name
     * @param mixed  $value
     */
    public function setValue($serviceName, $value)
    {
        // No op
        // Will not be implemented, consider to separate interfaces for storing and providing-only
    }
}
