<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Marketplace\Request;

use XCart\Marketplace\Constant;
use XCart\Marketplace\IValidator;
use XCart\Marketplace\Validator\SchemaList;

class Notifications extends AAPIRequest
{
    /**
     * @return string
     */
    public function getAction(): ?string
    {
        return Constant::ACTION_GET_XC5_NOTIFICATIONS;
    }

    /**
     * @return IValidator
     */
    public function getValidator(): IValidator
    {
        return new SchemaList(
            [
                Constant::FIELD_NOTIFICATION_TYPE        => [
                    'filter'  => FILTER_VALIDATE_REGEXP,
                    'options' => ['regexp' => Constant::REGEXP_WORD],
                ],
                Constant::FIELD_NOTIFICATION_MODULE      => [
                    'filter'  => FILTER_VALIDATE_REGEXP,
                    'options' => ['regexp' => Constant::REGEXP_WORD],
                ],
                Constant::FIELD_NOTIFICATION_IMAGE       => FILTER_SANITIZE_URL,
                Constant::FIELD_NOTIFICATION_TITLE       => [
                    'filter'  => FILTER_VALIDATE_REGEXP,
                    'options' => ['regexp' => Constant::REGEXP_WORD],
                ],
                Constant::FIELD_NOTIFICATION_DESCRIPTION => [
                    'filter'  => FILTER_VALIDATE_REGEXP,
                    'options' => ['regexp' => Constant::REGEXP_WORD],
                ],
                Constant::FIELD_NOTIFICATION_LINK        => FILTER_SANITIZE_URL,
                Constant::FIELD_NOTIFICATION_DATE        => [
                    'filter'  => FILTER_VALIDATE_INT,
                    'options' => ['min_range' => 0],
                ],
            ]
        );
    }
}
