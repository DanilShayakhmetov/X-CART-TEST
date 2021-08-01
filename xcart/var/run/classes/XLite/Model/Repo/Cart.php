<?php
namespace XLite\Model\Repo;
/**
 * Cart repository
 *
 * @Api\Operation\Create(modelClass="XLite\Model\Cart", summary="Add new cart")
 * @Api\Operation\Read(modelClass="XLite\Model\Cart", summary="Retrieve cart by id")
 * @Api\Operation\ReadAll(modelClass="XLite\Model\Cart", summary="Retrieve carts by conditions")
 * @Api\Operation\Update(modelClass="XLite\Model\Cart", summary="Update cart by id")
 * @Api\Operation\Delete(modelClass="XLite\Model\Cart", summary="Delete cart by id")
 */
class Cart extends \XLite\Module\XPay\XPaymentsCloud\Model\Repo\Cart {}