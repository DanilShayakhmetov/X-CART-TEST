<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use XCart\Bus\Client\MarketplaceClient;
use XCart\Bus\Helper\UrlBuilder;
use XCart\Bus\Query\Data\LicenseDataSource;
use XCart\Bus\Query\Data\MarketplaceModulesDataSource;
use XCart\ConfigParser\ConfigParser;
use XCart\SilexAnnotations\Annotations\Router;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service()
 * @Router\Controller()
 */
class AfterPurchase
{
    /**
     * @var UrlBuilder
     */
    private $urlBuilder;

    /**
     * @var MarketplaceClient
     */
    private $marketplaceClient;

    /**
     * @var LicenseDataSource
     */
    private $licenseDataSource;

    /**
     * @var MarketplaceModulesDataSource
     */
    private $marketplaceModulesDataSource;

    /**
     * @var bool
     */
    private $isTrial;

    /**
     * @var bool
     */
    private $isCloud;

    /**
     * @var ConfigParser
     */
    private $configParser;

    /**
     * @param Application                  $app
     * @param UrlBuilder                   $urlBuilder
     * @param MarketplaceClient            $marketplaceClient
     * @param LicenseDataSource            $licenseDataSource
     *
     * @param MarketplaceModulesDataSource $marketplaceModulesDataSource
     *
     * @return static
     *
     * @Service\Constructor
     * @codeCoverageIgnore
     */
    public static function serviceConstructor(
        Application $app,
        UrlBuilder $urlBuilder,
        MarketplaceClient $marketplaceClient,
        LicenseDataSource $licenseDataSource,
        MarketplaceModulesDataSource $marketplaceModulesDataSource
    ) {
        return new self(
            $urlBuilder,
            $marketplaceClient,
            $licenseDataSource,
            $marketplaceModulesDataSource,
            $app['config']['root_dir'],
            $app['config']['trial.is_enabled'],
            $app['xc_config']['service']['is_cloud'] ?? false

        );
    }

    /**
     * @param UrlBuilder                   $urlBuilder
     * @param MarketplaceClient            $marketplaceClient
     * @param LicenseDataSource            $licenseDataSource
     * @param MarketplaceModulesDataSource $marketplaceModulesDataSource
     * @param string                       $rootDir
     * @param bool                         $isTrial
     * @param bool                         $isCloud
     */
    public function __construct(
        UrlBuilder $urlBuilder,
        MarketplaceClient $marketplaceClient,
        LicenseDataSource $licenseDataSource,
        MarketplaceModulesDataSource $marketplaceModulesDataSource,
        $rootDir,
        $isTrial,
        $isCloud
    ) {
        $this->urlBuilder                   = $urlBuilder;
        $this->marketplaceClient            = $marketplaceClient;
        $this->licenseDataSource            = $licenseDataSource;
        $this->marketplaceModulesDataSource = $marketplaceModulesDataSource;
        $this->isTrial                      = $isTrial;
        $this->isCloud                      = $isCloud;

        $this->configParser = new ConfigParser($_SERVER, $rootDir . '/etc/');
    }

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @Router\Route(
     *     @Router\Request(method="GET", uri="/afterPurchase"),
     *     @Router\Before("XCart\Bus\Controller\Auth:authChecker")
     * )
     */
    public function indexAction(Request $request): Response
    {
        return $this->getAfterPurchaseResponse($request);
    }

    /**
     * @param Request $request
     * @param string  $moduleAuthor
     * @param string  $moduleName
     *
     * @return Response
     *
     * @Router\Route(
     *     @Router\Request(method="GET", uri="/afterPurchase/{moduleAuthor}/{moduleName}"),
     *     @Router\Before("XCart\Bus\Controller\Auth:authChecker")
     * )
     */
    public function purchaseButtonAction(Request $request, string $moduleAuthor, string $moduleName): Response
    {
        return $this->getAfterPurchaseResponse($request, $moduleAuthor, $moduleName);
    }

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @Router\Route(
     *     @Router\Request(method="GET", uri="/afterCloudPurchase"),
     *     @Router\Before("XCart\Bus\Controller\Auth:authChecker")
     * )
     */
    public function purchaseCloudEditionAction(Request $request): Response
    {
        return $this->getAfterCloudPurchaseResponse($request);
    }

    /**
     * @param Request     $request
     * @param string|null $moduleAuthor
     * @param string|null $moduleName
     *
     * @return Response
     */
    private function getAfterPurchaseResponse(Request $request, $moduleAuthor = null, $moduleName = null): Response
    {
        $response = new RedirectResponse(
            $moduleAuthor && $moduleName
                ? $this->urlBuilder->buildServiceUrl('my-purchases')
                : $this->urlBuilder->buildAdminUrl() . '?target=main'
        );

        try {
            $token = $request->get('token');

            $this->processToken($token, $moduleAuthor, $moduleName);
        } catch (\Exception $e) {
        }

        return $response;
    }

    /**
     * @param Request     $request
     *
     * @return Response
     */
    private function getAfterCloudPurchaseResponse(Request $request): Response
    {
        $response = new RedirectResponse(
            $this->urlBuilder->buildServiceMainUrl('changeCloudEdition')
        );

        try {
            $token = $request->get('token');

            $this->processToken($token);
        } catch (\Exception $e) {
        }

        return $response;
    }


    /**
     * @param string      $token
     * @param string|null $moduleAuthor
     * @param string|null $moduleName
     */
    private function processToken($token, $moduleAuthor = null, $moduleName = null): void
    {
        $tokenData = $this->marketplaceClient->getTokenData($token);

        if (!empty($tokenData['purchase']) || !empty($tokenData['prolongation'])) {
            $key      = $tokenData['purchase'][0] ?? $tokenData['prolongation'][0];
            $keysInfo = $this->marketplaceClient->getLicenseInfo($key);

            if ($keysInfo && $keysInfo[$key]) {
                $keysInfo = $keysInfo[$key];

                foreach ($keysInfo as $info) {
                    if (LicenseDataSource::KEY_TYPE_CORE !== (int) $info['keyType']) {
                        $this->licenseDataSource->removePending($info);

                        $licence = $this->licenseDataSource->findBy([
                            'author' => $info['author'],
                            'name'   => $info['name'],
                        ]);

                        if ($licence) {
                            $licence['keyValue'] = $key;
                            $licence['active']   = true;
                            $licence['keyData']  = $info['keyData'];
                            $this->licenseDataSource->saveOne($licence);
                        } else {
                            $info['keyValue'] = $key;
                            $info['active']   = true;
                            $this->licenseDataSource->saveOne($info);
                        }
                    } else {
                        $info['keyValue'] = $key;
                        $info['active']   = true;
                        $this->licenseDataSource->saveOne($info);

                        if ($this->isTrial && $this->isCloud) {
                            $this->configParser->setOption('service', 'is_trial', 'Off');
                        }
                    }
                }
            }
        } elseif ($moduleAuthor && $moduleName) {
            $this->licenseDataSource->savePending($moduleAuthor, $moduleName);
        }
    }
}
