<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Utils;

class PropertyBag
{
    /**
     * internal extra property bag
     * @var array
     */
    protected $properties = [];

    /**
     * ScriptState constructor.
     *
     * @param array $data data to set
     */
    public function __construct(array $data = null)
    {
        null === $data ?: $this->merge($data);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->properties;
    }

    /**
     * Append data
     *
     * @param array $data data to set
     *
     * @return self
     */
    public function merge(array $data)
    {
        $this->properties = array_merge_recursive($this->properties, $data);

        return $this;
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
        return isset($this->properties[$name]) ? $this->properties[$name] : null;
    }

    /**
     * Set property value
     *
     * @param string $name  property name
     * @param mixed  $value property value
     *
     * @return void
     */
    public function __set($name, $value)
    {
        $this->properties[$name] = $value;
    }

    /**
     * Check if property exists
     *
     * @param string $name property name
     *
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->properties[$name]);
    }

    /**
     * Unset property
     *
     * @param string $name property name
     *
     * @return void
     */
    public function __unset($name)
    {
        unset($this->properties[$name]);
    }
}
