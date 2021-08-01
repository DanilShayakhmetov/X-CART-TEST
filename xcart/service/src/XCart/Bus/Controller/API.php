<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Controller;

use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Schema;
use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use XCart\Bus\Auth\TokenService;
use XCart\Bus\Auth\XC5LoginService;
use XCart\Bus\Query\Context;
use XCart\Bus\Query\GraphQLExecutor;
use XCart\Bus\Query\Resolver\LanguageDataResolver;
use XCart\Bus\Query\Types\MutationType;
use XCart\Bus\Query\Types\QueryType;
use XCart\SilexAnnotations\Annotations\Router;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service()
 * @Router\Controller()
 */
class API
{
    /**
     * @var TokenService
     */
    private $tokenService;

    /**
     * @var QueryType
     */
    private $queryType;

    /**
     * @var MutationType
     */
    private $mutationType;

    /**
     * @var GraphQLExecutor
     */
    private $executor;

    /**
     * @var LanguageDataResolver
     */
    private $languageDataResolver;

    /**
     * @var XC5LoginService
     */
    private $xc5LoginService;

    /**
     * @var Context
     */
    private $context;

    /**
     * @var bool
     */
    private $demoMode;

    /**
     * @var Schema
     */
    private $schema;

    /**
     * @param Application          $app
     * @param TokenService         $tokenService
     * @param QueryType            $queryType
     * @param MutationType         $mutationType
     * @param GraphQLExecutor      $executor
     * @param LanguageDataResolver $languageDataResolver
     * @param XC5LoginService      $xc5LoginService
     * @param Context              $context
     *
     * @return API
     *
     * @Service\Constructor
     * @codeCoverageIgnore
     */
    public static function serviceConstructor(
        Application $app,
        TokenService $tokenService,
        QueryType $queryType,
        MutationType $mutationType,
        GraphQLExecutor $executor,
        LanguageDataResolver $languageDataResolver,
        XC5LoginService $xc5LoginService,
        Context $context
    ) {
        return new self(
            $tokenService,
            $queryType,
            $mutationType,
            $executor,
            $languageDataResolver,
            $xc5LoginService,
            $context,
            $app['xc_config']['demo']['demo_mode'] ?? false
        );
    }

    /**
     * @param TokenService         $tokenService
     * @param QueryType            $queryType
     * @param MutationType         $mutationType
     * @param GraphQLExecutor      $executor
     * @param LanguageDataResolver $languageDataResolver
     * @param XC5LoginService      $xc5LoginService
     * @param Context              $context
     * @param bool                 $demoMode
     */
    public function __construct(
        TokenService $tokenService,
        QueryType $queryType,
        MutationType $mutationType,
        GraphQLExecutor $executor,
        LanguageDataResolver $languageDataResolver,
        XC5LoginService $xc5LoginService,
        Context $context,
        $demoMode
    ) {
        $this->tokenService         = $tokenService;
        $this->queryType            = $queryType;
        $this->mutationType         = $mutationType;
        $this->executor             = $executor;
        $this->languageDataResolver = $languageDataResolver;
        $this->xc5LoginService      = $xc5LoginService;
        $this->context              = $context;
        $this->demoMode             = $demoMode;
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @Router\Route(
     *     @Router\Request(method="MATCH", uri="/api"),
     *     @Router\Before("XCart\Bus\Controller\API:authChecker"),
     *     @Router\After("XCart\Bus\Controller\Auth:touchCookie")
     * )
     */
    public function indexAction(Request $request): ?JsonResponse
    {
        try {
            $tokenData = $this->tokenService->decodeToken($request->cookies->get('bus_token'));

            if ($this->demoMode) {
                $this->context->mode = Context::ACCESS_MODE_READ | Context::ACCESS_MODE_READ_LICENSE;

            } elseif (($tokenData[TokenService::TOKEN_READ_ONLY] ?? false) === true) {
                $this->context->mode = Context::ACCESS_MODE_READ;

            } else {
                $this->context->mode = Context::ACCESS_MODE_FULL;
            }

            $languages          = $this->languageDataResolver->getLanguages(null, [], null, new ResolveInfo([]));
            $cookieLanguageCode = $request->cookies->get('locale', 'en');

            $this->context->languageCode = in_array($cookieLanguageCode, $languages, true) ? $cookieLanguageCode : 'en';

            if (isset($tokenData['admin_login'])) {
                $this->context->adminEmail = $tokenData['admin_login'];
            }

            $standardMode = false;

            $queries = $request->request->all();
            if (!empty($queries['query']) && empty($queries[0]['query'])) {
                $standardMode = true;
            }

            if ($standardMode) {
                $queries = [$queries];
            }

            $response = [];
            foreach ($queries as $query) {
                $response[] = $this->prepareQueryResponse(
                    $query['query'] ?? null,
                    $query['variables'] ?? null,
                    $query['operationName'] ?? null
                );
            }

            if ($standardMode) {
                $response = array_pop($response);
            }

            return new JsonResponse($response, 200);

        } catch (\Exception $e) {
            return new JsonResponse(null, 500);
        }
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse|null
     */
    public function authChecker(Request $request): ?JsonResponse
    {
        $tokenData = $this->tokenService->decodeToken($request->cookies->get('bus_token'));
        if (!$tokenData) {
            return new JsonResponse(null, 401);
        }

        if (!empty($tokenData['admin_login'])) {
            $xc5Cookie = $request->cookies->get(
                $this->xc5LoginService->getCookieName()
            );

            if (!$xc5Cookie) {
                return new JsonResponse(null, 401);
            }
        }

        return null;
    }

    /**
     * @return Schema
     */
    private function buildSchema(): Schema
    {
        if (!$this->schema) {
            $this->schema = new Schema([
                'query'    => $this->queryType,
                'mutation' => $this->mutationType,
            ]);
        }

        return $this->schema;
    }

    /**
     * @param string $query
     * @param array  $variables
     * @param string $operationName
     *
     * @return array|\GraphQL\Executor\Promise\Promise
     */
    private function prepareQueryResponse($query, $variables, $operationName)
    {
        try {
            return $this->executor->executeQuery(
                $this->buildSchema(),
                $query,
                $variables,
                $this->context,
                $operationName
            );

        } catch (\Exception $e) {
            return [
                'errors' => [
                    [
                        'message' => $e->getMessage(),
                    ],
                ],
            ];
        }
    }
}
