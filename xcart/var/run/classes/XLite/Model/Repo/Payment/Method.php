<?php
namespace XLite\Model\Repo\Payment;
/**
 * Payment method repository
 *
 * @Api\Operation\Read(modelClass="XLite\Model\Payment\Method", summary="Retrieve payment method by id")
 * @Api\Operation\ReadAll(modelClass="XLite\Model\Payment\Method", summary="Retrieve payment methods by conditions")
 * @Api\Operation\Update(modelClass="XLite\Model\Payment\Method", summary="Update payment method by id")
 */
class Method extends \XLite\Module\CDev\Paypal\Model\Repo\Payment\Method {}