<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core;

use Doctrine\Common\Cache\CacheProvider;
use Doctrine\Common\Cache\RedisCache;
use XLite\Core\Cache\FilesystemCache;

/**
 * Cache decorator
 */
class Cache extends \XLite\Base
{
    const REDIS_DEFAULT_PORT = 6379;
    // in seconds
    const DRIVER_CONNECTION_TIMEOUT = 1;

    /**
     * Cache driver
     *
     * @var \Doctrine\Common\Cache\CacheProvider
     */
    protected $driver;

    /**
     * Options
     *
     * @var array
     */
    protected $options;

    /**
     * Cache providers query
     *
     * @var array
     */
    protected static $cacheProvidersQueue;

    /**
     * Returns providers [provider_code => detector_closure] list
     *
     * @return array
     */
    protected function defineCacheProvidersQueue()
    {
        return [
            'redis' => function() {
                return !empty($this->options['servers']);
            },

            'apcu' => function () {
                return function_exists('apcu_cache_info');
            },

            'xcache' => function () {
                return function_exists('xcache_get');
            },

            'memcached' => function () {
                return !empty($this->options['servers'])
                    && class_exists('\Memcached')
                    && extension_loaded('memcached');
            },

            'memcache' => function () {
                return !empty($this->options['servers'])
                    && class_exists('\Memcache');
            },

            'file' => null,
        ];
    }

    /**
     * Returns providers => detector closure list
     *
     * @return array
     */
    protected function defineCacheBuilders()
    {
        return [
            'redis'     => 'buildRedisDriver',
            'apcu'      => 'buildAPCuDriver',
            'xcache'    => 'buildXcacheDriver',
            'memcached' => 'buildMemcachedDriver',
            'memcache'  => 'buildMemcacheDriver',
            'file'      => 'buildFileDriver',
        ];
    }

    /**
     * @param array $builders
     *
     * @return array
     */
    protected function prepareBuilders(array $builders)
    {
        foreach ($builders as $k => $builder) {
            if (is_string($builder)) {
                $builders[$k] = [$this, $builder];
            }
        }

        return $builders;
    }

    /**
     * @param      $code
     *
     * @param bool $silentTest
     *
     * @return null|\Doctrine\Common\Cache\CacheProvider
     */
    protected function buildCacheDriver($code, $silentTest = true)
    {
        $builders = $this->prepareBuilders($this->defineCacheBuilders());

        if (!empty($builders[$code])) {
            $cache = $builders[$code]();

            if ($cache && $this->testDriver($cache, $silentTest)) {
                return $cache;
            }
        }

        return null;
    }

    /**
     * @param array $queue
     *
     * @return array
     */
    protected function prepareCacheProvidersQueue(array $queue)
    {
        foreach ($queue as $k => $detector) {
            if (empty($detector)) {
                $queue[$k] = function () {
                    return true;
                };
            } elseif (is_string($detector)) {
                $queue[$k] = [$this, $detector];
            }
        }

        return $queue;
    }

    /**
     * @return array
     */
    protected function getCacheProvidersQueue()
    {
        if (is_null(static::$cacheProvidersQueue)) {
            static::$cacheProvidersQueue = $this->prepareCacheProvidersQueue(
                $this->defineCacheProvidersQueue()
            );
        }

        return static::$cacheProvidersQueue;
    }

    /**
     * Constructor
     *
     * @param \Doctrine\Common\Cache\CacheProvider $driver  Driver OPTIONAL
     * @param array                                $options Driver options OPTIONAL
     *
     * @return void
     */
    public function __construct(\Doctrine\Common\Cache\CacheProvider $driver = null, array $options = [])
    {
        $this->options = $options;
        $this->driver = $driver ?: $this->detectDriver();
    }

    /**
     * Get driver
     *
     * @return \Doctrine\Common\Cache\CacheProvider
     */
    public function getDriver()
    {
        return $this->driver;
    }

    /**
     * Returns default cache ttl in seconds
     *
     * @return int
     */
    public static function getDefaultCacheTtl()
    {
        return (int)\XLite\Core\ConfigParser::getOptions(['cache', 'default_cache_ttl'])
            ?: 604800;
    }

    /**
     * Call driver's method
     *
     * @param string $name      Method name
     * @param array  $arguments Arguments OPTIONAL
     *
     * @return mixed
     */
    public function __call($name, array $arguments = [])
    {
        return call_user_func_array([$this->driver, $name], $arguments);
    }

    /**
     * Detect Memcache cache driver
     *
     * @return boolean
     */
    protected static function detectCacheDriverRedis()
    {
        return class_exists('\Redis');
    }

    /**
     * Check driver functionality
     *
     * @param CacheProvider $driver
     * @param bool          $silent
     *
     * @return bool
     */
    protected function testDriver(CacheProvider $driver, $silent = true)
    {
        $key = '__test__';
        $value = rand(~PHP_INT_MAX, PHP_INT_MAX);

        if (!$driver) {
            return false;
        }

        try {
            if (
                !$driver->save($key, $value)
                || !$driver->contains($key)
                || (int)$driver->fetch($key) !== $value
            ) {
                return false;
            }

            if (
                !$driver->delete($key)
                || $driver->contains($key)
            ) {
                return false;
            }
        } catch (\Throwable $e) {
            if (!$silent) {
                \XLite\Logger::getInstance()->logPostponed(
                    sprintf(
                        'Cache driver "%s" error: %s',
                        get_class($driver),
                        $e->getMessage()
                    ),
                    LOG_WARNING
                );
            }

            return false;
        }

        return true;
    }

    /**
     * Get cache driver by options list
     *
     * @return \Doctrine\Common\Cache\CacheProvider
     */
    protected function detectDriver()
    {
        $options = \XLite::getInstance()->getOptions('cache');

        if (empty($options) || !is_array($options) || !isset($options['type'])) {
            $options = ['type' => null];
        }

        $this->options += $options;
        $type = $this->options['type'];

        $queue = $this->getCacheProvidersQueue();

        if ('auto' == $type) {
            foreach ($queue as $code => $detector) {
                if (
                    $detector()
                    && $cache = $this->buildCacheDriver($code)
                ) {
                    break;
                }
            }
        } elseif (!empty($queue[$type]) && $detector = $queue[$type]) {
            if ($detector()) {
                $cache = $this->buildCacheDriver($type, false);
            }

            if (empty($cache)) {
                $cache = $this->buildCacheDriver('file', false);
            }
        }

        if (empty($cache)) {
            $cache = new \Doctrine\Common\Cache\ArrayCache();
        }

        $namespace = $this->getNamespace();
        if (!empty($namespace)) {
            $cache->setNamespace($namespace);
        }

        return $cache;
    }

    /**
     * Get namespace
     *
     * @return string
     */
    protected function getNamespace()
    {
        $namespace = empty($this->options['namespace'])
            ? ''
            : ($this->options['namespace'] . '_');

        if (isset($this->options['original'])) {
            $namespace .= \Includes\Decorator\Utils\CacheManager::getDataCacheSuffix($this->options['original']);

        } else {
            $namespace .= \Includes\Decorator\Utils\CacheManager::getDataCacheSuffix();
        }

        return $namespace;
    }

    // {{{ Builders

    /**
     * Build APCu driver
     *
     * @return  \Doctrine\Common\Cache\CacheProvider
     */
    protected function buildAPCuDriver()
    {
        return new \Doctrine\Common\Cache\ApcuCache;
    }

    /**
     * Build Memcache driver
     *
     * @return  \Doctrine\Common\Cache\CacheProvider
     */
    protected function buildMemcacheDriver()
    {
        $servers = explode(';', $this->options['servers']) ?: ['localhost'];
        $memcache = new \Memcache();
        foreach ($servers as $row) {
            $row = trim($row);
            $tmp = explode(':', $row, 2);
            if ('unix' == $tmp[0]) {
                $memcache->addServer($row, 0);

            } elseif (isset($tmp[1])) {
                $memcache->addServer($tmp[0], $tmp[1]);

            } else {
                $memcache->addServer($tmp[0]);
            }
        }

        $cache = new \Doctrine\Common\Cache\MemcacheCache;
        $cache->setMemcache($memcache);

        return $cache;
    }

    /**
     * Build Memcache driver
     *
     * @return  \Doctrine\Common\Cache\CacheProvider
     */
    protected function buildMemcachedDriver()
    {
        $servers = explode(';', $this->options['servers']) ?: ['localhost'];
        $memcached = new \Memcached();
        foreach ($servers as $row) {
            $row = trim($row);
            $tmp = explode(':', $row, 2);
            if ('unix' == $tmp[0]) {
                $memcached->addServer($row, 0);

            } else {
                $memcached->addServer($tmp[0], isset($tmp[1]) ? $tmp[1] : 11211);
            }
        }

        $cache = new \Doctrine\Common\Cache\MemcachedCache;
        $cache->setMemcached($memcached);

        return $cache;
    }

    /**
     * Build Xcache driver
     *
     * @return  \Doctrine\Common\Cache\CacheProvider
     */
    protected function buildXcacheDriver()
    {
        return new \Doctrine\Common\Cache\XcacheCache;
    }

    /**
     * Build Redis driver
     *
     * @return  \Doctrine\Common\Cache\CacheProvider
     */
    protected function buildRedisDriver()
    {
        $servers = explode(';', $this->options['servers']) ?: ['localhost'];
        $row = $servers[0];

        if (!class_exists('\Redis')) {
            \XLite\Logger::getInstance()->logPostponed('Failure connecting with Redis: Class \Redis not found', LOG_WARNING);

            return null;
        }

        try {
            $redis = new \Redis();

            $tmp = explode(':', trim($row), 2);
            if ($tmp[0] === 'unix') {
                $result = $redis->connect($tmp[1], self::REDIS_DEFAULT_PORT, self::DRIVER_CONNECTION_TIMEOUT);
            } elseif (isset($tmp[1])) {
                $result = $redis->connect($tmp[0], $tmp[1], self::DRIVER_CONNECTION_TIMEOUT);
            } else {
                $result = $redis->connect($tmp[0], self::REDIS_DEFAULT_PORT, self::DRIVER_CONNECTION_TIMEOUT);
            }

            if (!$result) {
                throw new \RedisException('Unknown error');
            }

            $driver = new RedisCache();
            $driver->setRedis($redis);
            return $driver;
        } catch (\RedisException $e) {
            \XLite\Logger::getInstance()->logPostponed(
                sprintf(
                    'Failure connecting with Redis: %s',
                    $e->getMessage()
                ),
                LOG_WARNING
            );
            return null;
        }
    }

    /**
     * Build filesystem cache driver
     *
     * @return  \Doctrine\Common\Cache\CacheProvider
     */
    protected function buildFileDriver()
    {
        try {
            return new FilesystemCache(LC_DIR_DATACACHE);
        } catch (\Exception $e) {
            \XLite\Logger::getInstance()->log($e->getMessage(), LOG_ERR, $e->getTrace());
        }

        return null;
    }
    // }}}

}
