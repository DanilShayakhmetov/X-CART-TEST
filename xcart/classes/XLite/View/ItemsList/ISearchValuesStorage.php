<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ItemsList;

/**
 * ISearchValuesStorage
 */
interface ISearchValuesStorage
{
    /**
     * Get param value
     *
     * @param string    $serviceName   Search condition service name
     * @param mixed     $value
     */
    public function setValue($serviceName, $value);

    /**
     * Get param value
     *
     * @param string    $serviceName   Search condition service name
     *
     * @return mixed
     */
    public function getValue($serviceName);

    /**
     * Update storage
     */
    public function update();

    /**
     * @param \XLite\View\ItemsList\ISearchValuesStorage $storage Fallback storage to use
     */
    public function setFallbackStorage(\XLite\View\ItemsList\ISearchValuesStorage $storage);

    /**
     * Return FallbackStorage
     *
     * @return ISearchValuesStorage
     */
    public function getFallbackStorage();
}
