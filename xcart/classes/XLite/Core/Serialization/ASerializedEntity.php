<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Serialization;


/**
 * Abstract Serialized Entity
 */
abstract class ASerializedEntity
{
    /**
     * SerializedModel constructor.
     *
     * @param SerializableEntity $entity
     */
    abstract public function __construct(SerializableEntity $entity);

    /**
     * @return null|SerializableEntity
     */
    abstract public function restore();
}