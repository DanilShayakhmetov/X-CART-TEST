<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Serialization;


/**
 * EntitySerializer
 */
class EntitySerializer
{
    /**
     * @param \XLite\Core\Serialization\SerializableEntity $entity
     *
     * @return \XLite\Core\Serialization\SerializedEntity
     * @throws \Exception
     */
    public static function serialize(SerializableEntity $entity)
    {
        if (!$entity->isSerializable()) {
            throw new \Exception('Entity cannot be serialised');
        }

        if ($entity instanceof \XLite\Model\AEntity) {
            return new \XLite\Core\Serialization\SerializedEntity($entity);
        }

        throw new \Exception('Not implemented yet');
    }

    /**
     * @param \XLite\Core\Serialization\ASerializedEntity $serializedEntity
     *
     * @return null|\XLite\Core\Serialization\SerializableEntity
     */
    public static function unserialize(\XLite\Core\Serialization\ASerializedEntity $serializedEntity)
    {
        return $serializedEntity->restore();
    }
}