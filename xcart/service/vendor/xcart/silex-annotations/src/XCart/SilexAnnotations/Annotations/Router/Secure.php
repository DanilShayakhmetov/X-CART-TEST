<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\SilexAnnotations\Annotations\Router;

use Silex\Application;
use Silex\Controller as SilexController;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;


/**
 * @Annotation
 * @Target({"CLASS", "METHOD", "ANNOTATION"})
 */
class Secure implements IRoute
{
    /**
     * @var mixed
     */
    public $role;

    /**
     * @param SilexController|ControllerCollection $controller
     */
    public function process($controller)
    {
        if (method_exists($controller, 'secure')) {
            $controller->secure($this->role);

        } else {
            $roles = $this->role;
            $controller->before(function (
                /** @noinspection PhpUnusedParameterInspection */
                SymfonyRequest $request,
                Application $app
            ) use ($roles) {
                /** @var AuthorizationChecker $security */
                $security = $app['security.authorization_checker'];
                if (!$security->isGranted($roles)) {

                    throw new AccessDeniedException();
                }
            });
        }
    }
}
