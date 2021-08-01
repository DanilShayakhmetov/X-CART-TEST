<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Repo\Order\Status;

/**
 * Shipping status repository
 *
 * @Api\Operation\Create(modelClass="XLite\Model\Order\Status\Shipping", summary="Add new shipping status")
 * @Api\Operation\Read(modelClass="XLite\Model\Order\Status\Shipping", summary="Retrieve shipping status by id")
 * @Api\Operation\ReadAll(modelClass="XLite\Model\Order\Status\Shipping", summary="Retrieve all shipping statuses")
 * @Api\Operation\Update(modelClass="XLite\Model\Order\Status\Shipping", summary="Update shipping status by id")
 * @Api\Operation\Delete(modelClass="XLite\Model\Order\Status\Shipping", summary="Delete shipping status by id")
 */
class Shipping extends \XLite\Model\Repo\Order\Status\AStatus
{
}
