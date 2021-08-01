<?php
namespace XLite\Model\Payment;
/**
 * Payment backend transaction
 *
 * @Entity
 * @Table  (name="payment_backend_transactions",
 *      indexes={
 *          @Index (name="td", columns={"transaction_id","date"})
 *      }
 * )
 */
class BackendTransaction extends \XLite\Module\CDev\Paypal\Model\Payment\BackendTransaction {}