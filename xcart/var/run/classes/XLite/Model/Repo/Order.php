<?php
namespace XLite\Model\Repo;
/**
 * Order repository
 *
 * @Api\Operation\Create(modelClass="XLite\Model\Order", summary="Add new order")
 * @Api\Operation\Read(modelClass="XLite\Model\Order", summary="Retrieve order by id")
 * @Api\Operation\ReadAll(modelClass="XLite\Model\Order", summary="Retrieve orders by conditions")
 * @Api\Operation\Update(modelClass="XLite\Model\Order", summary="Update order by id")
 * @Api\Operation\Delete(modelClass="XLite\Model\Order", summary="Delete order by id")
 */
class Order extends \XLite\Module\CDev\Egoods\Model\Repo\Order {}