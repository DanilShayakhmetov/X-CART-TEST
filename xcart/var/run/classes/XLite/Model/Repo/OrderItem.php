<?php
namespace XLite\Model\Repo;
/**
 * The "order_item" model repository
 *
 * @Api\Operation\Create(modelClass="XLite\Model\OrderItem", summary="Add new order item")
 * @Api\Operation\Read(modelClass="XLite\Model\OrderItem", summary="Retrieve order item by id")
 * @Api\Operation\ReadAll(modelClass="XLite\Model\OrderItem", summary="Retrieve order items by conditions")
 * @Api\Operation\Update(modelClass="XLite\Model\OrderItem", summary="Update order item by id")
 * @Api\Operation\Delete(modelClass="XLite\Model\OrderItem", summary="Delete order item by id")
 */
class OrderItem extends \XLite\Module\XC\Reviews\Model\Repo\OrderItem {}