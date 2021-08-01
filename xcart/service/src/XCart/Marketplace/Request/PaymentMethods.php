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

class PaymentMethods extends AAPIRequest
{
    /**
     * @return string
     */
    public function getAction(): ?string
    {
        return 'get_payment_methods';
    }

    /**
     * @return IValidator
     */
    public function getValidator(): IValidator
    {
        return new SchemaList(
            [
                'service_name'    => [
                    'filter'  => FILTER_VALIDATE_REGEXP,
                    'options' => ['regexp' => Constant::REGEXP_WORD],
                ],
                'class'           => [
                    'filter'  => FILTER_VALIDATE_REGEXP,
                    'options' => ['regexp' => Constant::REGEXP_CLASS],
                ],
                'type'            => [
                    'filter'  => FILTER_VALIDATE_REGEXP,
                    'options' => ['regexp' => Constant::REGEXP_WORD],
                ],
                'orderby'         => [
                    'filter'  => FILTER_VALIDATE_REGEXP,
                    'options' => ['regexp' => Constant::REGEXP_WORD],
                ],
                'countries'       => [
                    'filter'  => FILTER_VALIDATE_REGEXP,
                    'flags'   => FILTER_REQUIRE_ARRAY,
                    'options' => ['regexp' => Constant::REGEXP_WORD],
                ],
                'exCountries'     => [
                    'filter'  => FILTER_VALIDATE_REGEXP,
                    'flags'   => FILTER_REQUIRE_ARRAY,
                    'options' => ['regexp' => Constant::REGEXP_WORD],
                ],
                'translations'    => [
                    'flags'   => FILTER_REQUIRE_ARRAY,
                ],
                'added'           => [
                    'filter'  => FILTER_VALIDATE_REGEXP,
                    'options' => ['regexp' => Constant::REGEXP_WORD],
                ],
                'enabled'         => [
                    'filter'  => FILTER_VALIDATE_REGEXP,
                    'options' => ['regexp' => Constant::REGEXP_WORD],
                ],
                'moduleEnabled'   => [
                    'filter'  => FILTER_VALIDATE_REGEXP,
                    'options' => ['regexp' => Constant::REGEXP_WORD],
                ],
                'moduleName'      => [
                    'filter'  => FILTER_VALIDATE_REGEXP,
                    'options' => ['regexp' => Constant::REGEXP_WORD],
                ],
                'fromMarketplace' => [
                    'filter'  => FILTER_VALIDATE_REGEXP,
                    'options' => ['regexp' => Constant::REGEXP_WORD],
                ],
                'iconURL'         => [
                    'filter'  => FILTER_VALIDATE_REGEXP,
                    'options' => ['regexp' => Constant::REGEXP_WORD],
                ],
                'modulePageURL'   => [
                    'filter'  => FILTER_VALIDATE_REGEXP,
                    'options' => ['regexp' => Constant::REGEXP_WORD],
                ],
            ]
        );
    }

    /**
     * @return array
     */
    protected function getDefaultParams(): array
    {
        return [
            'currentCoreVersion' => ['major' => ''],
            'shopCountryCode'    => '',
        ];
    }
}
