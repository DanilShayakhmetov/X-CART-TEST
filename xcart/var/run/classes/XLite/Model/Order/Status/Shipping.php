<?php
namespace XLite\Model\Order\Status;
/**
 * Shipping status
 *
 * @Entity
 * @Table  (name="order_shipping_statuses",
 *      indexes={
 *          @Index (name="code", columns={"code"})
 *      }
 * )
 */
class Shipping extends \XLite\Module\CDev\Egoods\Model\Order\Status\Shipping {}