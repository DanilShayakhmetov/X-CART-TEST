<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Repo\Order;

/**
 * Order surcharges repository
 *
 * @Api\Operation\Create(modelClass="XLite\Model\Order\Surcharge", summary="Add new order surcharge")
 * @Api\Operation\Read(modelClass="XLite\Model\Order\Surcharge", summary="Retrieve order surcharge by id")
 * @Api\Operation\ReadAll(modelClass="XLite\Model\Order\Surcharge", summary="Retrieve all order surcharges")
 * @Api\Operation\Update(modelClass="XLite\Model\Order\Surcharge", summary="Update order surcharge by id")
 * @Api\Operation\Delete(modelClass="XLite\Model\Order\Surcharge", summary="Delete order surcharge by id")
 */
class Surcharge extends \XLite\Model\Repo\Base\Surcharge
{
}

