<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ItemsList;

/**
 * ASearchValuesStorage
 */
abstract class ASearchValuesStorage implements ISearchValuesStorage
{
    /**
     * @var \XLite\View\ItemsList\ISearchValuesStorage    $fallbackStorage
     */
    protected $fallbackStorage;

    /**
     * Get param value
     *
     * @param string    $serviceName   Search condition service name
     *
     * @return mixed
     */
    abstract protected function getInnerValue($serviceName);

    /**
     * Update inner storage
     */
    abstract protected function updateInner();

    /**
     * Get param value
     *
     * @param string    $serviceName   Search condition service name
     *
     * @return mixed
     */
    public function getValue($serviceName)
    {
        $value = $this->getInnerValue($serviceName);

        if (null === $value && $this->fallbackStorage) {
            $value = $this->fallbackStorage->getValue($serviceName);
        }

        return $value;
    }

    public function setFallbackStorage(\XLite\View\ItemsList\ISearchValuesStorage $storage)
    {
        $this->fallbackStorage = $storage;
    }

    public function getFallbackStorage()
    {
        return $this->fallbackStorage;
    }

    /**
     * @param ISearchValuesStorage $storage
     */
    public function passFallbackStorage(\XLite\View\ItemsList\ISearchValuesStorage $storage)
    {
        $currentStorage = $this;
        while ($fallback = $currentStorage->getFallbackStorage()) {
            $currentStorage = $fallback;
        }

        $currentStorage->setFallbackStorage($storage);
    }

    /**
     * Update storage
     */
    public function update()
    {
        if ($this->fallbackStorage) {
            $this->fallbackStorage->update();
        }
        $this->updateInner();
    }
}
