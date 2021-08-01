<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Query\Resolver;

use GraphQL\Type\Definition\ResolveInfo;
use XCart\Bus\Core\Annotations\Resolver;
use XCart\Bus\Query\Data\IDataSource;
use XCart\Bus\Query\Data\PaymentMethodsDataSource;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service()
 */
class PaymentMethodsResolver
{
    /**
     * @var IDataSource
     */
    private $dataSource;

    /**
     * @param PaymentMethodsDataSource $dataSource
     */
    public function __construct(PaymentMethodsDataSource $dataSource)
    {
        $this->dataSource = $dataSource;
    }

    /** @noinspection MoreThanThreeArgumentsInspection */

    /**
     * @param             $value
     * @param             $args
     * @param             $context
     * @param ResolveInfo $info
     *
     * @return array
     *
     * @Resolver()
     */
    public function getList($value, $args, $context, ResolveInfo $info)
    {
        return $this->dataSource->getForCountry($args['countryCode'] ?? '');
    }
}
