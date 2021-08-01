<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Serialization;

use Exception;
use ReflectionClass;
use XLite\Core\Serialization\Deserializer\SchemaArrayObject;
use XLite\Core\Serialization\Deserializer\SchemaGroup;
use XLite\Core\Serialization\Deserializer\SchemaClassObject;
use XLite\Core\Serialization\Deserializer\SchemaReference;
use XLite\Core\Serialization\Deserializer\SchemaRoot;

/**
 * Class Deserializer
 * @package XLite\Core\Serialization
 * @see     http://www.phpinternalsbook.com/php5/classes_objects/serialization.html
 */
class Deserializer
{
    /**
     * @var array
     */
    protected $references = [];

    /**
     * unserialize analog without bug https://bugs.php.net/bug.php?id=77302
     *
     * @param string $serialized
     *
     * @return array|mixed|object
     * @throws Exception
     */
    public static function deserialize(string $serialized)
    {
        $deserializer = new self();

        $schema = $deserializer->parseSchema($serialized);

        return $deserializer->mapSchema($schema);
    }

    /**
     * @param mixed $data
     *
     * @return array|mixed|object
     * @throws Exception
     */
    public function mapSchema($data)
    {
        if ($data instanceof SchemaRoot) {
            return $this->mapSchema($data->getProp());
        }

        if ($data instanceof SchemaReference) {
            return $this->references[$data->getIndex()];
        }

        if ($data instanceof SchemaClassObject) {
            return $this->createNewObject($data);
        }

        if ($data instanceof SchemaArrayObject) {
            $array = [];

            foreach ($data->getProps() as $key => $value) {
                $array[$key] = $this->mapSchema($value);
            }

            return $array;
        }

        return $data;
    }

    /**
     * @param string $body
     *
     * @return SchemaRoot
     */
    public function parseSchema(string $body): SchemaRoot
    {
        $chars = str_split($body);

        $string = 0;
        $buffer = '';

        $root = new SchemaRoot();

        $currentObject = $root;

        foreach ($chars as $position => $char) {
            if ($string > 0) {
                $buffer .= $char;
                $string--;
                continue;
            }

            if ($char === '"') {
                $firstChar = preg_match("/[sO]:(\d+):$/", $buffer, $matches);

                if ($firstChar) {
                    $string = $matches[1] + 1;
                }
            } elseif ($char === '{') {
                $object = $this->createSchemaGroup($currentObject, $buffer);

                $currentObject->addProp($object);
                $currentObject = $object;

                continue;
            } elseif ($char === '}') {
                $this->assignProp($buffer, $currentObject);
                $currentObject = $currentObject->getParent();

                continue;
            } elseif ($char === ';') {
                $this->assignProp($buffer, $currentObject);

                continue;
            }

            $buffer .= $char;
        }

        if (!empty($buffer)) {
            $this->assignProp($buffer, $currentObject);
        }

        return $root;
    }

    /**
     * @param string      $buffer
     * @param SchemaGroup $object
     */
    protected function assignProp(string &$buffer, SchemaGroup $object): void
    {
        $object->addProp($buffer);
        $buffer = '';
    }

    /**
     * @param SchemaGroup $object
     * @param string      $buffer
     *
     * @return SchemaGroup
     */
    protected function createSchemaGroup(SchemaGroup $object, string &$buffer): SchemaGroup
    {
        if (strpos($buffer, 'O') === 0) {
            $childObject = new SchemaClassObject($buffer, $object);
        } else {
            $childObject = new SchemaArrayObject($buffer, $object);
        }

        $buffer = '';

        return $childObject;
    }

    /**
     * @param SchemaClassObject $classObject
     *
     * @return object
     * @throws Exception
     */
    protected function createNewObject(SchemaClassObject $classObject)
    {
        $reflection = new ReflectionClass($classObject->getClassName());
        $class      = $reflection->newInstanceWithoutConstructor();

        $this->references[$classObject->getIndex()] = $class;

        if ($reflection->hasMethod('__wakeup')) {
            $class->__wakeup();
        }

        $props = [];

        $restoreAsMethod = $reflection->hasMethod('__userialize');

        foreach ($classObject->getProps() as $key => $schema) {
            preg_match('/^(\0(.*)\0)?(.*)$/u', $key, $matches);
            $propClass = $matches[2] ?? null;
            $propName  = $matches[3];

            $value = $this->mapSchema($schema);

            if (!$restoreAsMethod) {
                $property = $this
                    ->getTargetReflection($reflection, $propClass)
                    ->getProperty($propName);

                $property->setAccessible(true);
                $property->setValue($class, $value);
            } else {
                $props[$propName] = $value;
            }
        }

        if ($restoreAsMethod) {
            $class->__unserialize($props);
        }

        return $class;
    }

    /**
     * @param ReflectionClass $reflection
     * @param string|null     $class
     *
     * @return ReflectionClass
     * @throws Exception
     */
    protected function getTargetReflection(ReflectionClass $reflection, string $class = null): ReflectionClass
    {
        if (empty($class) || $class === '*' || $reflection->getName() === $class) {
            return $reflection;
        }

        $parent = $reflection->getParentClass();

        if (!$parent) {
            throw new Exception("Could not find target class '$class'");
        }

        if ($parent->getName() === $class) {
            return $parent;
        }

        return $this->getTargetReflection($parent, $class);
    }
}