<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Mail\Common;


use XLite\Core\Converter;
use XLite\Core\Mailer;
use XLite\Model\Payment\Transaction;

class FailedTransactionAdmin extends \XLite\Core\Mail\AMail
{
    static function getInterface()
    {
        return \XLite::ADMIN_INTERFACE;
    }

    static function getDir()
    {
        return 'failed_transaction';
    }

    public function __construct(Transaction $transaction)
    {
        parent::__construct();

        $this->setFrom(Mailer::getOrdersDepartmentMail());
        $this->setTo(Mailer::getOrdersDepartmentMails());

        if ($profile = $transaction->getProfile()) {
            $this->addReplyTo($profile->getLogin());
        }

        $transactionSearchURL = Converter::buildFullURL(
            'payment_transactions',
            '',
            [
                'public_id' => $transaction->getPublicId(),
            ],
            \XLite::getAdminScript()
        );

        $this->appendData([
            'transactionSearchURL' => $transactionSearchURL,
            'transaction'          => $transaction,
            'order'                => $transaction->getOrder(),
        ]);
    }
}