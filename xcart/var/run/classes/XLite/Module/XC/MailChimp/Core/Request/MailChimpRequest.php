<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\Core\Request;

use XLite\Core\Request;
use XLite\Module\XC\MailChimp\Core\MailChimpECommerce;

class MailChimpRequest implements IMailChimpRequest
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $method;

    /**
     * @var string
     */
    protected $action;

    /**
     * @var array
     */
    protected $args = [];

    /**
     * @var int
     */
    protected $timeout;

    /**
     * @param string $name
     * @param string $method
     * @param string $action
     * @param array  $args
     * @param int    $timeout
     */
    public function __construct($name, $method, $action, array $args = [], $timeout = 10)
    {
        $this->name    = $name;
        $this->method  = $method;
        $this->action  = $action;
        $this->args    = $args;
        $this->timeout = $timeout;
    }

    public static function getTrackingIdFromRequest(): ?string
    {
        /** @var \XLite\Core\Request|\XLite\Module\XC\MailChimp\Core\Request $request */
        $request = Request::getInstance();

        return $request->{$request::MAILCHIMP_TRACKING_CODE} ?? null;
    }

    /**
     * @return mixed
     */
    public function schedule()
    {
        return Scheduler::schedule($this);
    }

    /**
     * @return array|null
     */
    public function execute(): ?array
    {
        return MailChimpECommerce::getInstance()->executeRequest($this);
    }

    /**
     * @return array
     */
    public function getOperation(): array
    {
        return [
            'method' => $this->getMethod(),
            'path'   => $this->getAction(),
            'body'   => json_encode($this->getArgs()),
        ];
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @param string $method
     */
    public function setMethod(string $method): void
    {
        $this->method = $method;
    }

    /**
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * @param string $action
     */
    public function setAction(string $action): void
    {
        $this->action = $action;
    }

    /**
     * @return array
     */
    public function getArgs(): array
    {
        return $this->args;
    }

    /**
     * @param array $args
     */
    public function setArgs(array $args): void
    {
        $this->args = $args;
    }

    /**
     * @return int
     */
    public function getTimeout(): int
    {
        return $this->timeout;
    }

    /**
     * @param int $timeout
     */
    public function setTimeout(int $timeout): void
    {
        $this->timeout = $timeout;
    }
}
