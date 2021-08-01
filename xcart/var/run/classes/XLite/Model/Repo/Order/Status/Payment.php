<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Repo\Order\Status;

/**
 * Payment status repository
 *
 * @Api\Operation\Create(modelClass="XLite\Model\Order\Status\Payment", summary="Add new payment status")
 * @Api\Operation\Read(modelClass="XLite\Model\Order\Status\Payment", summary="Retrieve payment status by id")
 * @Api\Operation\ReadAll(modelClass="XLite\Model\Order\Status\Payment", summary="Retrieve all payment statuses")
 * @Api\Operation\Update(modelClass="XLite\Model\Order\Status\Payment", summary="Update payment status by id")
 * @Api\Operation\Delete(modelClass="XLite\Model\Order\Status\Payment", summary="Delete payment status by id")
 */
class Payment extends \XLite\Model\Repo\Order\Status\AStatus
{
}
