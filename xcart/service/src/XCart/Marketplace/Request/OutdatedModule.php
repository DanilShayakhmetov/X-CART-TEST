<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Marketplace\Request;

use XCart\Marketplace\Constant;
use XCart\Marketplace\IValidator;
use XCart\Marketplace\Validator\Schema;

class OutdatedModule extends AAPIRequest
{
    /**
     * @return string
     */
    public function getAction(): ?string
    {
        return Constant::ACTION_REQUEST_FOR_UPGRADE;
    }

    /**
     * @return IValidator
     */
    public function getValidator(): IValidator
    {
        return new Schema([
            Constant::FIELD_IS_REQUEST_FOR_UPGRADE_SENT => \FILTER_VALIDATE_BOOLEAN,
        ]);
    }

    /**
     * @param mixed $data
     * @param array $headers
     *
     * @return mixed
     */
    public function formatData($data, array $headers = [])
    {
        return isset($data['isRequestForUpgradeSent']) && $data['isRequestForUpgradeSent'];
    }

    /**
     * @return array
     */
    protected function getDefaultParams(): array
    {
        return [
            Constant::FIELD_EMAIL   => '',
            Constant::FIELD_MODULES => [],
        ];
    }
}
