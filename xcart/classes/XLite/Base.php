<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite;

use XLite\Core\Exception\MethodNotFound;

/**
 * Base class
 * FIXME - must be abstract
 * FIXME - must extends \XLite\the Base\SuperClass
 */
class Base extends \XLite\Base\Singleton
{
    /**
     * Singletons accessible directly from each object (see the "__get" method)
     *
     * @var array
     */
    protected static $singletons = array(
        'xlite'    => 'XLite',
        'auth'     => '\XLite\Core\Auth',
        'session'  => '\XLite\Core\Session',
        'logger'   => '\XLite\Logger',
        'config'   => '\XLite\Core\Config',
        'layout'   => '\XLite\Core\Layout',
        'mailer'   => '\XLite\Core\Mailer',
    );


    /**
     * "Magic" getter. It's called when object property is not found
     * FIXME - backward compatibility
     *
     * @param string $name Property name
     *
     * @return mixed
     */
    public function __get($name)
    {
        return isset(self::$singletons[$name])
            ? call_user_func(array(self::$singletons[$name], 'getInstance'))
            : null;
    }

    /**
     * "Magic" caller. It's called when object method is not found
     *
     * @param string $method Method to call
     * @param array  $args   Call arrguments OPTIONAL
     *
     * @throws MethodNotFound
     */
    public function __call($method, array $args = [])
    {
        throw new MethodNotFound(
            'Trying to call undefined class method;'
            . ' class - "' . get_class($this) . '", function - "' . $method . '"',
            get_class($this),
            $method,
            $args
        );
    }

    /**
     * Returns property value named $name. If no property found, returns null
     *
     * @param string $name Property name
     *
     * @return mixed
     */
    public function get($name)
    {
        $result = null;

        if (method_exists($this, 'get' . $name)) {
            $func = 'get' . $name;

            // 'get' + property name
            $result = $this->$func();

        } elseif (method_exists($this, 'is' . $name)) {
            $func = 'is' . $name;

            // 'is' + property name
            $result = $this->$func();

        } else {
            $result = $this->$name;
        }

        return $result;
    }

    /**
     * Get array of keys which should be setted without calling 'set<Name>' method
     * 
     * @return array
     */
    protected function getForcedKeys()
    {
        return array();
    }

    /**
     * check if 'set<Name>' method should be called
     * 
     * @param string $name  Property name
     * 
     * @return boolean
     */
    protected function forceSet($name)
    {
        return in_array($name, $this->getForcedKeys());
    }

    /**
     * Set object property
     *
     * @param string $name  Property name
     * @param mixed  $value Property value
     *
     * @return void
     */
    public function set($name, $value)
    {
        if (method_exists($this, 'set' . $name) && !$this->forceSet($name)) {
            $func = 'set' . $name;

            // 'set' + property name
            $this->$func($value);

        } else {
            $this->$name = $value;
        }
    }

    /**
     * Returns boolean property value named $name. If no property found, returns null
     *
     * @param mixed $name Property name
     *
     * @return boolean
     */
    public function is($name)
    {
        return (bool) $this->get($name);
    }

    /**
     * Maps the specified associative array to this object properties
     *
     * @param array $assoc Array(properties) to set
     *
     * @return void
     */
    public function setProperties(array $assoc)
    {
        foreach ($assoc as $key => $value) {
            $this->set($key, $value);
        }
    }
}
