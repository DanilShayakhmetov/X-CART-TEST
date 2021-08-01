<?php

namespace XCart\SilexAnnotationsTest\Fixtures\Controller;

use XCart\SilexAnnotations\Annotations\Router;
use Exception;
use Silex\Application;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGenerator;

/**
 * @Router\Controller(prefix="/test")
 */
class TestController
{
    /**
     * @Router\Route(
     *      @Router\Request(method="GET", uri="test1")
     * )
     */
    public function test1()
    {
        return new Response();
    }

    /**
     * @Router\Request(method="POST", uri="/post")
     */
    public function testPostRequest()
    {
        return new Response();
    }

    /**
     * @Router\Request(method="PUT", uri="/put")
     */
    public function testPutRequest()
    {
        return new Response();
    }

    /**
     * @Router\Request(method="DELETE", uri="/delete")
     */
    public function testDeleteRequest()
    {
        return new Response();
    }

    /**
     * @Router\Request(method="MATCH", uri="/match")
     */
    public function testMatchRequest()
    {
        return new Response();
    }

    /**
     * @Router\Request(method="GET|POST", uri="/multi-method")
     */
    public function testMultiMethodRequest()
    {
        return new Response();
    }

    /**
     * @Router\Request(method="GET", uri="firstRequest")
     * @Router\Request(method="GET", uri="secondRequest")
     */
    public function testMultipleRequests()
    {
        return new Response();
    }

    /**
     * The assert modifier should be applied to both endpoints.
     *
     * @Router\Request(method="GET", uri="firstRequest/{num}")
     * @Router\Request(method="GET", uri="secondRequest/{num}")
     * @Router\Assert(variable="num", regex="\d+")
     */
    public function testMultipleRequestsShareModifiers()
    {
        return new Response();
    }

    /**
     * @Router\Request(method="GET", uri="assert/{var}")
     * @Router\Assert(variable="var", regex="\d+")
     */
    public function assertTest($var)
    {
        return new Response($var);
    }

    /**
     * @Router\Request(method="GET", uri="/before")
     * @Router\Before("XCart\SilexAnnotationsTest\Fixtures\Controller\TestController::beforeCallback")
     */
    public function beforeTest()
    {
        return new Response();
    }

    /**
     * @Router\Request(method="GET", uri="/after")
     * @Router\After("XCart\SilexAnnotationsTest\Fixtures\Controller\TestController::afterCallback")
     */
    public function afterTest()
    {
        return new Response();
    }

    /**
     * @Router\Request(method="GET", uri="/bind")
     * @Router\Bind(routeName="testRouteName")
     */
    public function bindTest(Application $app)
    {
        /** @var UrlGenerator $urlGenerator */
        $urlGenerator = $app['url_generator'];
        return new Response($urlGenerator->generate('testRouteName'));
    }

    /**
     * @Router\Request(method="GET", uri="/convert/{var}")
     * @Router\Convert(variable="var", callback="XCart\SilexAnnotationsTest\Fixtures\Controller\TestController::convert")
     */
    public function convertTest($var)
    {
        return new Response($var);
    }

    /**
     * @Router\Request(method="GET", uri="/hostTest")
     * @Router\Host("www.test.com")
     */
    public function testHost()
    {
        return new Response();
    }

    /**
     * @Router\Request(method="GET", uri="/requirehttp")
     * @Router\RequireHttp
     */
    public function testRequireHttp()
    {
        return new Response();
    }

    /**
     * @Router\Request(method="GET", uri="/requirehttps")
     * @Router\RequireHttp
     */
    public function testRequireHttps()
    {
        return new Response();
    }

    /**
     * @Router\Request(method="GET", uri="/secure")
     * @Router\Secure("ROLE_ADMIN")
     */
    public function testSecure()
    {
        return new Response();
    }

    /**
     * @Router\Request(method="GET", uri="/assert/modifier/{var}")
     * @Router\Modifier(method="assert", args={"var", "\d+"})
     */
    public function testAssertModifier($var)
    {
        return new Response($var);
    }

    /**
     * @Router\Request(method="GET", uri="/requirehttps/modifier")
     * @Router\Modifier("requireHttps")
     */
    public function testRequireHttpsModifier()
    {
        return new Response();
    }

    /**
     * @Router\Request(method="GET", uri="/host/modifier")
     * @Router\Modifier(method="host", args="www.wronghost.com")
     */
    public function testHostModifier()
    {
        return new Response();
    }

    /**
     * @Router\Route(
     *      @Router\Request(method="GET", uri="/route/{var}"),
     *      @Router\Assert(variable="var", regex="\d+")
     * )
     *
     * @Router\Route(
     *      @Router\Request(method="GET", uri="/route2/{var}")
     * )
     */
    public function routeTest($var)
    {
        return new Response($var);
    }

    public static function beforeCallback()
    {
        throw new Exception('before callback');
    }

    public static function afterCallback()
    {
        throw new Exception('after callback');
    }

    public static function convert($var)
    {
        return $var + 5;
    }
} 