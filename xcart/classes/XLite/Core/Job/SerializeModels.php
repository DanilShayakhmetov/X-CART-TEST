<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Job;

use ReflectionClass;
use ReflectionProperty;
use XLite\Model\AEntity;

trait SerializeModels
{
    /**
     * Prepare the instance for serialization.
     *
     * @return array
     */
    public function __sleep()
    {
        $properties = (new ReflectionClass($this))->getProperties();

        foreach ($properties as $property) {
            $property->setValue($this, $this->getSerializedPropertyValue(
                $this->getPropertyValue($property)
            ));
        }

        return array_map(function ($p) {
            return $p->getName();
        }, $properties);
    }

    /**
     * Restore the model after serialization.
     *
     * @return void
     */
    public function __wakeup()
    {
        foreach ((new ReflectionClass($this))->getProperties() as $property) {
            $property->setValue($this, $this->getRestoredPropertyValue(
                $this->getPropertyValue($property)
            ));
        }
    }

    /**
     * Get the property value for the given property.
     *
     * @param  \ReflectionProperty  $property
     * @return mixed
     */
    protected function getPropertyValue(ReflectionProperty $property)
    {
        $property->setAccessible(true);

        return $property->getValue($this);
    }

    /**
     * Get the property value prepared for serialization.
     *
     * @param  mixed  $value
     * @return mixed
     */
    protected function getSerializedPropertyValue($value)
    {
        if ($value instanceof AEntity) {
            $em = \XLite\Core\Database::getEM();
            try {
                return $em->getReference(get_class($value), $value->getUniqueIdentifier());
            } catch(\Doctrine\ORM\ORMException $e) {
                return $value;
            }
        }

        if (is_array($value)) {
            $result = [];
            foreach ($value as $key => $item) {
                $result[$key] = $this->getSerializedPropertyValue($item);
            }
            $value = $result;
        }

        return $value;
    }

    /**
     * Get the restored property value after deserialization.
     *
     * @param  mixed  $value
     * @return mixed
     */
    protected function getRestoredPropertyValue($value)
    {
        if ($value instanceof AEntity) {
            if ($value instanceof \XLite\Model\Order) {
                $value = \XLite\Core\Database::getRepo('\XLite\Model\Order')->find($value->getUniqueIdentifier());

            } else {
                $foundValue = $value->getRepository()->find($value->getUniqueIdentifier());

                if ($foundValue) {
                    $value = $foundValue;
                }
            }

            return $value;
        }

        if (is_array($value)) {
            $result = [];
            foreach ($value as $key => $item) {
                $result[$key] = $this->getRestoredPropertyValue($item);
            }
            $value = $result;
        }

        return $value;
    }

}
