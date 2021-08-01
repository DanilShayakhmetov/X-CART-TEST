<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\Model\Payment;

 class BackendTransaction extends \XLite\Module\XPay\XPaymentsCloud\Model\Payment\BackendTransaction implements \XLite\Base\IDecorator
{
    const TRAN_TYPE_PAYOUT = 'payout';
}
