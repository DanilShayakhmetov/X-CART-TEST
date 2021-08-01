<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model;

/**
 * Abstract caching factory
 */
class CachingFactory extends \XLite\Model\Factory
{
    /**
     * Objects cache
     *
     * @var array
     */
    protected static $cache = array();


    /**
     * Cache and return a result of object method call
     *
     * @param string  $signature  Result key in cache
     * @param mixed   $handler    Callback object
     * @param string  $method     Method to call
     * @param array   $args       Callback arguments OPTIONAL
     * @param boolean $clearCache Clear cache flag OPTIONAL
     *
     * @return mixed
     */
    public static function getObjectFromCallback($signature, $handler, $method, array $args = array(), $clearCache = false)
    {
        if (!isset(static::$cache[$signature]) || $clearCache) {
            static::$cache[$signature] = call_user_func_array(array(static::prepareHandler($handler), $method), $args);
        }

        return static::$cache[$signature];
    }

    /**
     * cache and return object instance
     *
     * @param string $signature Result key in cache
     * @param string $class     Object class name
     * @param array  $args      Constructor arguments OPTIONAL
     *
     * @return \XLite\Base
     */
    public static function getObject($signature, $class, array $args = array())
    {
        return static::getObjectFromCallback($signature, 'self', 'create', array($class, $args));
    }

    /**
     * Clear cache cell
     *
     * @param string $signature Cache cell key
     *
     * @return void
     */
    public static function clearCacheCell($signature)
    {
        unset(static::$cache[$signature]);
    }

    /**
     * Clear cache
     *
     * @return void
     */
    public static function clearCache()
    {
        static::$cache = null;
    }


    /**
     * Get handler object (or pseudo-constant)
     *
     * @param mixed $handler Variable to prepare
     *
     * @return mixed
     */
    protected static function prepareHandler($handler)
    {
        return (!is_object($handler) && !in_array($handler, array('self', 'parent', 'static')))
            ? new $handler()
            : $handler;
    }


    /**
     * Clean up cache
     *
     * @return void
     */
    public function __destruct()
    {
        static::clearCache();
    }
}
