<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Marketplace\Logger;

final class Dumper
{
    /**
     * @param mixed   $data
     * @param integer $depth
     *
     * @return array|string|\stdClass
     */
    public static function export($data, $depth = 0)
    {
        $result = null;

        if ($depth) {
            if (is_array($data)) {
                $result = [];
                foreach ((array) $data as $k => $v) {
                    $result[$k] = self::export($v, $depth - 1);
                }

                return $result;
            }

            if (is_object($data)) {
                $result = new \stdClass();

                if ($data instanceof \DateTime) {
                    $result->__CLASS__ = 'DateTime';
                    $result->date      = $data->format('c');
                    $result->timezone  = $data->getTimezone()->getName();

                } else {
                    $class             = get_class($data);
                    $result->__CLASS__ = $class;

                    if ($data instanceof \ArrayObject || $data instanceof \ArrayIterator) {
                        $result->__STORAGE__ = self::export($data->getArrayCopy(), $depth - 1);
                    }

                    try {
                        $reflector = new \ReflectionClass($data);
                        foreach ($reflector->getProperties() as $property) {
                            $name = $property->getName();

                            $property->setAccessible(true);
                            $result->$name = self::export($property->getValue($data), $depth - 1);
                        }
                    } catch (\ReflectionException $e) {
                        $result->__ERROR__ = $e->getMessage();
                    }
                }

                return $result;
            }

            return '(' . gettype($data) . ') ' . (string) $data;
        }

        return self::toScalar($data);
    }

    private static function toScalar($data)
    {
        if (is_object($data)) {

            return get_class($data);
        }

        if (is_array($data)) {

            return 'Array(' . count($data) . ')';
        }

        return (string) $data;
    }
}
