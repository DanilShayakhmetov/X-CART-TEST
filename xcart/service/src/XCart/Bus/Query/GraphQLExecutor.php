<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Query;

use GraphQL\Error\Error;
use GraphQL\Executor\Promise\Promise;
use GraphQL\GraphQL;
use XCart\Bus\Controller\ErrorFormatter;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service()
 */
class GraphQLExecutor
{
    /** @noinspection MoreThanThreeArgumentsInspection */

    /**
     * @param $schema
     * @param $query
     * @param $variables
     * @param $context
     * @param $operationName
     *
     * @return array|Promise
     */
    public function executeQuery($schema, $query, $variables, $context, $operationName = null)
    {
        return GraphQL::executeQuery($schema, $query, null, $context, $variables, $operationName)
            ->setErrorFormatter(static function (Error $error) {
                return ErrorFormatter::createFromException($error, true);
            })
            ->toArray();
    }
}
