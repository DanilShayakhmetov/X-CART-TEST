<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Job;


use XLite\Core\Mail\AMail;

class SendMail extends JobAbstract
{
    protected $mail;

    public function __construct(AMail $mail)
    {
        parent::__construct();

        $this->mail = $mail;
    }

    public function getName()
    {
        return 'Mail #' . $this->id;
    }

    public function handle()
    {
        $this->markAsStarted();
        $this->mail->send();
        $this->markAsFinished();
    }
}
