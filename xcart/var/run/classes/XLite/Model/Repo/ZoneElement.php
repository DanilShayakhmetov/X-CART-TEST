<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Repo;

/**
 * Zone element repository
 *
 * @Api\Operation\Create(modelClass="XLite\Model\ZoneElement", summary="Add new shipping zone element")
 * @Api\Operation\Read(modelClass="XLite\Model\ZoneElement", summary="Retrieve shipping zone element by id")
 * @Api\Operation\ReadAll(modelClass="XLite\Model\ZoneElement", summary="Retrieve all shipping zone elements")
 * @Api\Operation\Update(modelClass="XLite\Model\ZoneElement", summary="Update shipping zone element by id")
 * @Api\Operation\Delete(modelClass="XLite\Model\ZoneElement", summary="Delete shipping zone element by id")
 */
class ZoneElement extends \XLite\Model\Repo\ARepo
{
    /**
     * Repository type
     *
     * @var string
     */
    protected $type = self::TYPE_SECONDARY;
}
