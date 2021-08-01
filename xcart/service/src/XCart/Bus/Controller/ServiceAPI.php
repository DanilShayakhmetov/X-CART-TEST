<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Controller;

use GraphQL\Type\Schema;
use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use XCart\Bus\Query\Context;
use XCart\Bus\Query\GraphQLExecutor;
use XCart\Bus\Query\Types\Service\MutationType;
use XCart\Bus\Query\Types\Service\QueryType;
use XCart\SilexAnnotations\Annotations\Router;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service()
 * @Router\Controller()
 */
class ServiceAPI
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var Context
     */
    protected $context;

    /**
     * @var GraphQLExecutor
     */
    protected $executor;

    /**
     * @var QueryType
     */
    protected $queryType;

    /**
     * @var MutationType
     */
    protected $mutationType;

    /**
     * @var Schema
     */
    private $schema;

    public function __construct(
        Application $app,
        Context $context,
        GraphQLExecutor $executor,
        QueryType $queryType,
        MutationType $mutationType
    ) {
        $this->app          = $app;
        $this->context      = $context;
        $this->executor     = $executor;
        $this->queryType    = $queryType;
        $this->mutationType = $mutationType;
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @Router\Route(
     *     @Router\Request(method="MATCH", uri="/service-api"),
     *     @Router\Before("XCart\Bus\Controller\ServiceAPI:permissionChecker"),
     * )
     */
    public function indexAction(Request $request): ?JsonResponse
    {
        try {
            $this->context->mode = Context::ACCESS_MODE_FULL;
            $response = $this->createResponse($request);

            return new JsonResponse($response, 200);
        } catch (\Exception $exception) {
            $error = $this->getExceptionResponse($exception);

            return new JsonResponse(['errors' => [$error]], $exception->getCode());
        }
    }

    /**
     * @return JsonResponse|null
     */
    public function permissionChecker(): ?JsonResponse
    {
        if (empty($this->app['xc_config']['service']['is_cloud'])) {
            return new JsonResponse(null, 401);
        }

        return null;
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    protected function createResponse(Request $request): array
    {
        $queries = $request->request->all();

        $standardMode = !empty($queries['query']) && empty($queries[0]['query']);

        if ($standardMode) {
            return $this->prepareQueryResponse($queries);
        }

        $response = [];
        foreach ($queries as $query) {
            $response[] = $this->prepareQueryResponse($query);
        }

        return $response;
    }

    /**
     * @param array $query
     *
     * @return array
     */
    protected function prepareQueryResponse($query): array
    {
        try {
            return (array) $this->executor->executeQuery(
                $this->buildSchema(),
                $query['query'] ?? null,
                $query['variables'] ?? null,
                $this->context,
                $query['operationName'] ?? null
            );
        } catch (\Exception $exception) {
            return [
                'errors' => [
                    $this->getExceptionResponse($exception),
                ],
            ];
        }
    }

    /**
     * @return Schema
     */
    private function buildSchema(): Schema
    {
        if ($this->schema === null) {
            $this->schema = new Schema([
                'query'    => $this->queryType,
                'mutation' => $this->mutationType,
            ]);
        }

        return $this->schema;
    }

    /**
     * @param \Exception $exception
     *
     * @return array
     */
    private function getExceptionResponse(\Exception $exception)
    {
        return [
            'message'   => $exception->getMessage(),
            'category'  => 'php',
            'locations' => [
                'line' => $exception->getLine(),
                'file' => $exception->getFile(),
            ],
        ];
    }
}