<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\Core\Request;

interface IMailChimpRequest
{
    /**
     * @return array|null
     */
    public function execute(): ?array;

    /**
     * @return mixed
     */
    public function schedule();

    public function getName(): string;

    public function getMethod(): string;

    public function getAction(): string;

    public function getArgs(): array;

    public function getTimeout(): int;
}
