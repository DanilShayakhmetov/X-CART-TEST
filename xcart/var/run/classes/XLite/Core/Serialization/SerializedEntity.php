<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Serialization;


/**
 * Serialized \XLite\Model\AEntity
 */
class SerializedEntity extends \XLite\Core\Serialization\ASerializedEntity
{
    /**
     * @var mixed
     */
    protected $uniqueIdentifier;
    /**
     * @var string
     */
    protected $repo;

    /**
     * @param \XLite\Model\AEntity|SerializableEntity $entity
     */
    public function __construct(SerializableEntity $entity)
    {
        $this->uniqueIdentifier = $entity->getUniqueIdentifier();
        $this->repo = $entity->getEntityName();
    }

    /**
     * @return null|\XLite\Model\AEntity
     */
    public function restore()
    {
        $repo = \XLite\Core\Database::getRepo($this->repo);

        return $repo && ($entity = $repo->find($this->uniqueIdentifier))
            ? $entity
            : null;
    }
}