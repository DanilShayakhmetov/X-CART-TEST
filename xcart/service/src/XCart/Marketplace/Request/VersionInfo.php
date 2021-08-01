<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Marketplace\Request;

use XCart\Marketplace\ITransport;
use XCart\Marketplace\IValidator;
use XCart\Marketplace\Validator\Callback;

class VersionInfo extends AAPIRequest
{
    /**
     * @return string
     */
    public function getAction(): ?string
    {
        return 'get_version_info';
    }

    /**
     * @return IValidator
     */
    public function getValidator(): IValidator
    {
        return new Callback(function ($data) {
            return (bool) $data;
        });
    }

    /**
     * @return array
     */
    protected function getDefaultParams(): array
    {
        return [
            'entities' => [],
        ];
    }
}
