<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\RESTAPI\Core\Schema\Complex;

/**
 * Order schema
 */
interface IModel
{
    /**
     * Convert model
     *
     * @param \XLite\Model\AEntity  $model            Entity
     * @param boolean               $withAssociations Convert with associations
     *
     * @return array
     */
    public function convertModel(\XLite\Model\AEntity $model, $withAssociations);

    /**
     * Prepare input
     *
     * @param array $data Data
     *
     * @return array
     */
    public function prepareInput(array $data);

    /**
     * Preload data
     *
     * @param \XLite\Model\AEntity $entity Product
     * @param array                $data   Data
     *
     * @return void
     */
    public function preloadData(\XLite\Model\AEntity $entity, array $data);
}
