<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Serialization;

use Doctrine\ORM\PersistentCollection;
use ReflectionClass;

class PreloadCollections
{
    private static $knownClasses = [];

    /**
     * Initialize all Doctrine\ORM\PersistentCollection in the $object
     *
     * @param $object
     *
     * @return mixed
     * @throws \ReflectionException
     */
    public static function get($object)
    {
        self::$knownClasses = [];
        self::preloadObject($object);

        return $object;
    }

    /**
     * @param $object
     *
     * @throws \ReflectionException
     */
    protected static function preloadObject($object): void
    {
        if (is_array($object)) {
            foreach ($object as $item) {
                self::preloadObject($item);
            }
        } elseif (is_object($object)) {
            $hash = spl_object_hash($object);

            if (isset(self::$knownClasses[$hash])) {
                return;
            }

            self::$knownClasses[$hash] = $hash;

            if ($object instanceof PersistentCollection) {
                foreach ($object as $item) {
                    self::preloadObject($item);
                }
            } else {
                $reflection = new ReflectionClass($object);
                self::preloadProps($reflection, $object);
            }
        }
    }

    /**
     * @param ReflectionClass $reflection
     * @param                 $object
     *
     * @throws \ReflectionException
     */
    protected static function preloadProps(ReflectionClass $reflection, $object): void
    {
        $props = $reflection->getProperties();

        foreach ($props as $property) {
            $property->setAccessible(true);
            $value = $property->getValue($object);
            self::preloadObject($value);
        }

        $parent = $reflection->getParentClass();

        if ($parent) {
            self::preloadProps($parent, $object);
        }
    }
}