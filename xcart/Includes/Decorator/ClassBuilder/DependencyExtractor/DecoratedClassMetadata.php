<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Decorator\ClassBuilder\DependencyExtractor;

class DecoratedClassMetadata implements \Serializable
{
    /**
     * @var string
     */
    private static $root = LC_DIR_ROOT;

    /**
     * @var string[]
     */
    private $decorators;

    /**
     * @param string[] $decorators
     */
    public function __construct($decorators)
    {
        $this->decorators = $decorators;
    }

    /**
     * @return string[]
     */
    public function getDecorators()
    {
        return $this->decorators;
    }

    /**
     * @param string $root
     */
    public static function setRoot($root)
    {
        static::$root = $root;
    }

    /**
     * @return string
     */
    public function serialize()
    {
        $decorators = array_map(function ($decorator) {
            return substr($decorator, strlen(static::$root));
        }, $this->decorators);

        return json_encode($decorators);
    }

    /**
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        $decorators = json_decode($serialized);

        $this->decorators = array_map(function ($decorator) {
            return static::$root . $decorator;
        }, $decorators);
    }
}
