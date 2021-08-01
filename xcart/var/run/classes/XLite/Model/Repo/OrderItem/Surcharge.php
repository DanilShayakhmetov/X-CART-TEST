<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Repo\OrderItem;

/**
 * Order item surcharges repository
 *
 * @Api\Operation\Create(modelClass="XLite\Model\OrderItem\Surcharge", summary="Add new order item surcharge")
 * @Api\Operation\Read(modelClass="XLite\Model\OrderItem\Surcharge", summary="Retrieve order item surcharge by id")
 * @Api\Operation\ReadAll(modelClass="XLite\Model\OrderItem\Surcharge", summary="Retrieve all order item surcharges")
 * @Api\Operation\Update(modelClass="XLite\Model\OrderItem\Surcharge", summary="Update order item surcharge by id")
 * @Api\Operation\Delete(modelClass="XLite\Model\OrderItem\Surcharge", summary="Delete order item surcharge by id")
 */
class Surcharge extends \XLite\Model\Repo\Base\Surcharge
{
}

