<?php
namespace XLite\Model\Payment;
/**
 * Payment transaction
 *
 * @Entity
 * @Table  (name="payment_transactions",
 *      indexes={
 *          @Index (name="status", columns={"status"}),
 *          @Index (name="o", columns={"order_id","status"}),
 *          @Index (name="pm", columns={"method_id","status"}),
 *          @Index (name="publicTxnId", columns={"publicTxnId"})
 *      }
 * )
 * @HasLifecycleCallbacks
 */
class Transaction extends \XLite\Module\CDev\Paypal\Model\Payment\Transaction {}