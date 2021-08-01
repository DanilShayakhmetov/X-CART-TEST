<?php
namespace XLite\Model\Order\Status;
/**
 * Payment status
 *
 * @Entity
 * @Table  (name="order_payment_statuses",
 *      indexes={
 *          @Index (name="code", columns={"code"})
 *      }
 * )
 */
class Payment extends \XLite\Module\CDev\GoogleAnalytics\Model\Order\Status\Payment {}