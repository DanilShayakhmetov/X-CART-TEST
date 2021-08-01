<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use XCart\Bus\Auth\EmergencyCodeService;
use XCart\Bus\Auth\TokenService;
use XCart\Bus\Auth\XC5LoginService;
use XCart\Bus\Exception\XC5Unavailable;
use XCart\Bus\Query\Data\CoreConfigDataSource;
use XCart\SilexAnnotations\Annotations\Router;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service()
 * @Router\Controller()
 */
class Auth
{
    private const ADMIN_TTL = 43200;
    private const LOGIN_ATTEMPTS = 5;
    private const LOGIN_LOCK_TTL = 120;

    /**
     * @var TokenService
     */
    private $tokenService;

    /**
     * @var EmergencyCodeService
     */
    private $emergencyCodeService;

    /**
     * @var XC5LoginService
     */
    private $xc5LoginService;

    /**
     * @var CoreConfigDataSource
     */
    private $coreConfigDataSource;

    /**
     * @var string
     */
    private $domain;

    /**
     * @var string
     */
    private $cookiePath;

    /**
     * @var bool
     */
    private $debug;

    /**
     * @var
     */
    private $secure;

    /**
     * @param Application          $app
     * @param TokenService         $tokenService
     * @param EmergencyCodeService $emergencyCodeService
     * @param XC5LoginService      $xc5LoginService
     * @param CoreConfigDataSource $coreConfigDataSource
     *
     * @return static
     *
     * @Service\Constructor
     * @codeCoverageIgnore
     */
    public static function serviceConstructor(
        Application $app,
        TokenService $tokenService,
        EmergencyCodeService $emergencyCodeService,
        XC5LoginService $xc5LoginService,
        CoreConfigDataSource $coreConfigDataSource
    ) {
        return new self(
            $tokenService,
            $emergencyCodeService,
            $xc5LoginService,
            $coreConfigDataSource,
            parse_url($app['config']['domain'], \PHP_URL_HOST),
            $app['config']['webdir'],
            (bool) $app['config']['debug'],
            $app['config']['scheme'] == 'https'
        );
    }

    /**
     * @param TokenService         $tokenService
     * @param EmergencyCodeService $emergencyCodeService
     * @param XC5LoginService      $xc5LoginService
     * @param CoreConfigDataSource $coreConfigDataSource
     * @param string               $domain
     * @param string               $cookiePath
     * @param string               $debug
     * @param string               $secure
     */
    public function __construct(
        TokenService $tokenService,
        EmergencyCodeService $emergencyCodeService,
        XC5LoginService $xc5LoginService,
        CoreConfigDataSource $coreConfigDataSource,
        $domain,
        $cookiePath,
        $debug,
        $secure
    ) {
        $this->tokenService         = $tokenService;
        $this->emergencyCodeService = $emergencyCodeService;
        $this->xc5LoginService      = $xc5LoginService;
        $this->coreConfigDataSource = $coreConfigDataSource;
        $this->domain               = $domain;
        $this->cookiePath           = $cookiePath;
        $this->debug                = $debug;
        $this->secure               = $secure;
    }

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @Router\Route(
     *     @Router\Request(method="MATCH", uri="/auth"),
     * )
     */
    public function indexAction(Request $request): Response
    {
        try {
            $authCode = $request->get('auth_code');
            $unsetXidToken = false;
            $additionalTokenData = [];

            if ($authCode) {
                $shouldGenerateJWT = false;

                if ($this->coreConfigDataSource->authLock < time()) {
                    if ($this->coreConfigDataSource->authAttempts > self::LOGIN_ATTEMPTS) {
                        $this->coreConfigDataSource->authAttempts = 0;
                    }

                    $shouldGenerateJWT = $this->emergencyCodeService->checkAuthCode($authCode);

                    if (!$shouldGenerateJWT && $this->emergencyCodeService->checkServiceCode(md5($authCode))) {
                        $shouldGenerateJWT                                  = true;
                        $additionalTokenData[TokenService::TOKEN_READ_ONLY] = true;
                        $unsetXidToken                                      = true;
                    }
                }

                if (!$shouldGenerateJWT) {
                    $authAttempts = $this->coreConfigDataSource->authAttempts + 1;
                    $this->coreConfigDataSource->authAttempts = $authAttempts;

                    if ($authAttempts > self::LOGIN_ATTEMPTS) {
                        $this->coreConfigDataSource->authLock = time() + self::LOGIN_LOCK_TTL;

                        return new JsonResponse(['authLock' => $this->coreConfigDataSource->authLock], 423);
                    }
                }
            } else {
                $xc5Cookie = $request->cookies->get(
                    $this->xc5LoginService->getCookieName()
                );

                $shouldGenerateJWT = $this->xc5LoginService->checkXC5Cookie($xc5Cookie);

                if (!$shouldGenerateJWT) {
                    return $request->isMethod('GET')
                        ? new RedirectResponse($this->xc5LoginService->getLoginURL())
                        : new JsonResponse(['redirectUrl' => $this->xc5LoginService->getLoginURL()], 403);
                }

                $verifyData = $this->xc5LoginService->getVerifyData($xc5Cookie);

                $additionalTokenData['admin_login'] = $verifyData['admin_login'] ?? '';
            }

            if ($shouldGenerateJWT) {

                $this->coreConfigDataSource->authAttempts = 0;
                $this->coreConfigDataSource->authLock = 0;

                $response = $request->isMethod('GET')
                    ? new RedirectResponse('service.php')
                    : new JsonResponse(null, 200);

                $cookie = $this->createCookie(
                    $this->tokenService->generateToken($additionalTokenData)
                );
                $response->headers->setCookie($cookie);

                $response->headers->setCookie(
                    new Cookie('unset_xid',
                        $unsetXidToken,
                        0,
                        $this->cookiePath ?: '/',
                        $this->domain,
                        $this->secure,
                        false,
                        false,
                        'lax'
                    )
                );

                return $response;
            }

            return new JsonResponse(null, 401);

        } catch (XC5Unavailable $e) {
            return $request->isMethod('GET')
                ? new RedirectResponse('service.php#/login')
                : new JsonResponse(null, 503);

        } catch (\Exception $e) {
            $error = null;

            if ($this->debug) {
                $error = $e->getMessage();
            }

            return new JsonResponse(null, 500, ['X-Error' => $error]);
        }
    }

    /**
     * @param Request $request
     *
     * @return Response|null
     */
    public function authChecker(Request $request): ?Response
    {
        $tokenData = $this->tokenService->decodeToken($request->cookies->get('bus_token'));
        if (!$tokenData) {
            return new Response(null, 401);
        }

        if (!empty($tokenData['admin_login'])) {
            $xc5Cookie = $request->cookies->get(
                $this->xc5LoginService->getCookieName()
            );

            if (!$xc5Cookie) {
                return new Response(null, 401);
            }
        }

        return null;
    }

    /**
     * @param $request
     * @param $response
     *
     * @return mixed
     */
    public function touchCookie(Request $request, Response $response)
    {
        if ($response->getStatusCode() === 200) {
            $unsetXid = $request->cookies->get('unset_xid');

            $xid = $request->cookies->get($this->xc5LoginService->getCookieName());
            if ($xid && !$unsetXid) {
                $response->headers->setCookie(
                    new Cookie($this->xc5LoginService->getCookieName(), $xid, time() + self::ADMIN_TTL, $this->cookiePath ?: '/', $this->domain, $this->secure, true, false, 'lax')
                );
            }

            $busToken = $request->cookies->get('bus_token');
            if ($busToken) {
                $response->headers->setCookie(
                    new Cookie('bus_token', $busToken, time() + self::ADMIN_TTL, $this->cookiePath ?: '/', $this->domain, $this->secure, true, false, 'lax')
                );
            }
        }

        return $response;
    }

    /**
     * @param string $jwt
     *
     * @return Cookie
     * @throws \InvalidArgumentException
     */
    private function createCookie($jwt): Cookie
    {
        $isHttpOnly = true;

        return new Cookie(
            'bus_token',
            $jwt,
            0,
            $this->cookiePath ?: '/',
            $this->domain,
            $this->secure,
            $isHttpOnly,
            false,
            'strict'
        );
    }
}
