<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\Core\Job;

use XLite\Core\Job\JobAbstract;
use XLite\Module\XC\MailChimp\Core\Request\IMailChimpRequest;

class ExecuteMailChimpRequest extends JobAbstract
{
    /**
     * @var IMailChimpRequest
     */
    protected $request;

    /**
     * @param IMailChimpRequest $request
     */
    public function __construct(IMailChimpRequest $request)
    {
        parent::__construct();

        $this->request = $request;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'Mailchimp #' . $this->id;
    }

    public function handle()
    {
        $this->markAsStarted();

        $this->request->execute();

        $this->markAsFinished();
    }

    /**
     * @return IMailChimpRequest
     */
    public function getRequest(): IMailChimpRequest
    {
        return $this->request;
    }

    /**
     * @param IMailChimpRequest $request
     */
    public function setRequest(IMailChimpRequest $request): void
    {
        $this->request = $request;
    }
}
