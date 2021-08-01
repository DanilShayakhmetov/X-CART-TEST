<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Marketplace\Request;

use XCart\Marketplace\Constant;
use XCart\Marketplace\ITransport;
use XCart\Marketplace\IValidator;
use XCart\Marketplace\Validator\Schema;

class AddonInfo extends AAPIRequest
{
    /**
     * @return array
     */
    public static function getResponseSchema(): array
    {
        return [
            Constant::FIELD_VERSION           => [
                'filter'  => \FILTER_VALIDATE_REGEXP,
                'flags'   => \FILTER_REQUIRE_ARRAY,
                'options' => ['regexp' => Constant::REGEXP_VERSION],
            ],
            Constant::FIELD_REVISION_DATE     => [
                'filter'  => \FILTER_VALIDATE_INT,
                'options' => [],
            ],
            Constant::FIELD_AUTHOR            => [
                'filter'  => \FILTER_VALIDATE_REGEXP,
                'options' => ['regexp' => Constant::REGEXP_WORD],
            ],
            Constant::FIELD_NAME              => [
                'filter'  => \FILTER_VALIDATE_REGEXP,
                'options' => ['regexp' => Constant::REGEXP_WORD],
            ],
            Constant::FIELD_READABLE_AUTHOR   => [
                'filter'  => \FILTER_VALIDATE_REGEXP,
                'options' => ['regexp' => Constant::REGEXP_WORD],
            ],
            Constant::FIELD_READABLE_NAME     => [
                'filter'  => \FILTER_VALIDATE_REGEXP,
                'options' => ['regexp' => Constant::REGEXP_WORD],
            ],
            Constant::FIELD_MODULE_ID         => [
                'filter'  => \FILTER_VALIDATE_REGEXP,
                'options' => ['regexp' => Constant::REGEXP_HASH],
            ],
            Constant::FIELD_DESCRIPTION       => [
                'filter'  => \FILTER_VALIDATE_REGEXP,
                'options' => ['regexp' => Constant::REGEXP_WORD],
            ],
            Constant::FIELD_PRICE             => \FILTER_VALIDATE_FLOAT,
            Constant::FIELD_CURRENCY          => [
                'filter'  => \FILTER_VALIDATE_REGEXP,
                'options' => ['regexp' => Constant::REGEXP_CURRENCY],
            ],
            Constant::FIELD_ICON_URL          => \FILTER_SANITIZE_URL,
            Constant::FIELD_LIST_ICON_URL     => \FILTER_SANITIZE_URL,
            Constant::FIELD_PAGE_URL          => \FILTER_SANITIZE_URL,
            Constant::FIELD_AUTHOR_PAGE_URL   => \FILTER_SANITIZE_URL,
            Constant::FIELD_RATING            => [
                'filter' => \FILTER_SANITIZE_NUMBER_FLOAT,
                'flags'  => \FILTER_REQUIRE_ARRAY | \FILTER_FLAG_ALLOW_FRACTION,
            ],
            Constant::FIELD_DEPENDENCIES      => [
                'filter'  => \FILTER_VALIDATE_REGEXP,
                'flags'   => \FILTER_REQUIRE_ARRAY,
                'options' => ['regexp' => Constant::REGEXP_CLASS],
            ],
            Constant::FIELD_DOWNLOADS_COUNT   => [
                'filter'  => \FILTER_VALIDATE_INT,
                'options' => ['min_range' => 0],
            ],
            Constant::FIELD_LENGTH            => [
                'filter'  => \FILTER_VALIDATE_INT,
                'options' => ['min_range' => 0],
            ],
            Constant::FIELD_XCN_PLAN          => [
                'filter'  => \FILTER_VALIDATE_INT,
                'options' => ['min_range' => -1],
            ],
            Constant::FIELD_TAGS              => [
                'filter'  => \FILTER_VALIDATE_REGEXP,
                'flags'   => \FILTER_REQUIRE_ARRAY,
                'options' => ['regexp' => Constant::REGEXP_CLASS],
            ],
            Constant::FIELD_AUTHOR_EMAIL      => [
                'filter'  => \FILTER_VALIDATE_REGEXP,
                'options' => ['regexp' => Constant::REGEXP_WORD],
            ],
            Constant::FIELD_IS_LANDING        => [
                'filter'  => \FILTER_VALIDATE_INT,
                'options' => ['min_range' => 0],
            ],
            Constant::FIELD_LANDING_POSITION  => [
                'filter'  => \FILTER_VALIDATE_INT,
                'options' => ['min_range' => -1],
            ],
            Constant::FIELD_MIN_CORE_VERSION  => [
                'filter'  => \FILTER_VALIDATE_INT,
                'options' => ['min_range' => 0],
            ],
            Constant::FIELD_EDITION_STATE     => [
                'filter'  => \FILTER_VALIDATE_INT,
                'options' => ['min_range' => 0],
            ],
            Constant::FIELD_EDITIONS          => [
                'filter'  => \FILTER_VALIDATE_REGEXP,
                'flags'   => \FILTER_REQUIRE_ARRAY,
                'options' => ['regexp' => Constant::REGEXP_CLASS],
            ],
            Constant::FIELD_XB_PRODUCT_ID     => [
                'filter'  => \FILTER_VALIDATE_INT,
                'options' => ['min_range' => 0],
            ],
            Constant::FIELD_PRIVATE           => [
                'filter'  => \FILTER_VALIDATE_INT,
                'options' => ['min_range' => 0],
            ],
            Constant::FIELD_WAVE              => [
                'filter'  => \FILTER_VALIDATE_INT,
                'options' => [],
            ],
            Constant::FIELD_SALES_CHANNEL_POS => [
                'filter'  => \FILTER_VALIDATE_INT,
                'options' => [],
            ],
            Constant::FIELD_LICENSE              => [
                'filter'  => \FILTER_VALIDATE_REGEXP,
                'options' => ['regexp' => Constant::REGEXP_TEXT],
            ],
        ];
    }

    /**
     * @return string
     */
    public function getAction(): ?string
    {
        return Constant::ACTION_GET_ADDON_INFO;
    }

    /**
     * @return IValidator
     */
    public function getValidator(): IValidator
    {
        return new Schema(static::getResponseSchema());
    }

    /**
     * @return array
     */
    protected function getDefaultParams(): array
    {
        return [
            Constant::FIELD_MODULE_ID => '',
            Constant::FIELD_KEY       => null,
        ];
    }
}
