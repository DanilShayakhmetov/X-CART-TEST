<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Client;

use Exception;
use Psr\Log\LoggerInterface;
use Silex\Application;
use XCart\Bus\Domain\Module;
use XCart\Bus\Exception\MarketplaceException;
use XCart\Bus\Query\Data\CoreConfigDataSource;
use XCart\Bus\Query\Data\InstalledModulesDataSource;
use XCart\Bus\Query\Data\LicenseDataSource;
use XCart\Marketplace;
use XCart\Marketplace\Constant;
use XCart\Marketplace\RangeIterator;
use XCart\Marketplace\Request\AddonHash;
use XCart\Marketplace\Request\AddonHashBatch;
use XCart\Marketplace\Request\Addons;
use XCart\Marketplace\Request\AddonsSearch;
use XCart\Marketplace\Request\Banners;
use XCart\Marketplace\Request\CheckAddonKey;
use XCart\Marketplace\Request\CoreHash;
use XCart\Marketplace\Request\Cores;
use XCart\Marketplace\Request\Editions;
use XCart\Marketplace\Request\GDPRModules;
use XCart\Marketplace\Request\GetTokenData;
use XCart\Marketplace\Request\Notifications;
use XCart\Marketplace\Request\OutdatedModule;
use XCart\Marketplace\Request\PaymentMethods;
use XCart\Marketplace\Request\ResendKey;
use XCart\Marketplace\Request\Set;
use XCart\Marketplace\Request\SetKeyWave;
use XCart\Marketplace\Request\ShippingMethods;
use XCart\Marketplace\Request\Tags;
use XCart\Marketplace\Request\Test;
use XCart\Marketplace\Request\VersionInfo;
use XCart\Marketplace\Request\Waves;
use XCart\Marketplace\Transport\TransportException;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service(arguments={"logger"="XCart\Bus\Core\Logger\Marketplace"})
 */
class MarketplaceClient
{
    /**
     * @var Application
     */
    private $app;

    /**
     * @var InstalledModulesDataSource
     */
    private $installedModulesDataSource;

    /**
     * @var CoreConfigDataSource
     */
    private $coreConfigDataSource;

    /**
     * @var LicenseDataSource
     */
    private $licenseDataSource;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var bool
     */
    private $isCloud;

    /**
     * @var Marketplace
     */
    private $marketplace;

    /**
     * @var Exception|null
     */
    private $lastError;

    /**
     * @param Application                $app
     * @param InstalledModulesDataSource $installedModulesDataSource
     * @param CoreConfigDataSource       $coreConfigDataSource
     * @param LicenseDataSource          $licenseDataSource
     * @param LoggerInterface            $logger
     */
    public function __construct(
        Application $app,
        InstalledModulesDataSource $installedModulesDataSource,
        CoreConfigDataSource $coreConfigDataSource,
        LicenseDataSource $licenseDataSource,
        LoggerInterface $logger
    ) {
        $this->app                        = $app;
        $this->installedModulesDataSource = $installedModulesDataSource;
        $this->coreConfigDataSource       = $coreConfigDataSource;
        $this->licenseDataSource          = $licenseDataSource;
        $this->logger                     = $logger;

        $this->isCloud = $app['xc_config']['service']['is_cloud'] ?? false;
    }

    /**
     * @return bool
     */
    public function getTest(): bool
    {
        try {
            $result = (bool) $this->getMarketplace()->getData(Test::class);
            if ($result) {
                $this->unlockMarketplace();
            }

            return $result;
        } catch (TransportException $exception) {
        } catch (MarketplaceException $exception) {
        }

        return false;
    }

    /**
     * @return array
     */
    public function getCores(): array
    {
        return $this->getData(Cores::class);
    }

    /**
     * @return array
     */
    public function getAllModules(): array
    {
        $params = [
            'modules' => serialize($this->installedModulesDataSource->getInstalledVersions()),
            'lng'     => implode(',', $this->installedModulesDataSource->getLanguages()),
        ];

        $core                 = $this->installedModulesDataSource->find('CDev-Core');
        $installationDate     = $core ? $core['installedDate'] : 0;
        $trialPeriodIsExpired = ($installationDate + $this->installedModulesDataSource::TRIAL_PERIOD - time()) <= 0;

        $coreLicense = $this->licenseDataSource->findBy([
            'author' => 'CDev',
            'name'   => 'Core',
        ]);

        if (!$coreLicense && !$trialPeriodIsExpired) {
            $params['trial'] = 1;
        }

        if ($this->coreConfigDataSource->adminEmail) {
            $params['email'] = $this->coreConfigDataSource->adminEmail;
        }

        $licenseKeys = $this->getModuleLicenseKeys();
        if ($licenseKeys) {
            $params['keys'] = $licenseKeys;
        }

        return $this->getData(Addons::class, $params);
    }

    /**
     * @param string  $substring
     * @param integer $wave
     *
     * @return array
     */
    public function searchModules($substring, $wave = null): array
    {
        return $this->getData(AddonsSearch::class, [
            'substring' => $substring,
            'wave'      => $wave,
        ]);
    }

    /**
     * @return array
     */
    public function getAllBanners(): array
    {
        return $this->getData(Banners::class);
    }

    /**
     * @return array
     */
    public function getAllTags(): array
    {
        return $this->getData(Tags::class);
    }

    /**
     * @return array
     */
    public function getWaves(): array
    {
        return $this->getData(Waves::class);
    }

    /**
     * @return array
     */
    public function getAllNotifications(): array
    {
        return $this->getData(Notifications::class);
    }

    /**
     * @param string $counryCode
     *
     * @return array
     */
    public function getAllPaymentMethods(string $counryCode = ''): array
    {
        $coreMajorVersion = '';

        $coreVersion = $this->coreConfigDataSource->version;
        if (preg_match('/(\d+\.\d+)\.(\d+)\.(\d+)/', $coreVersion, $matches)) {
            $coreMajorVersion = $matches[1];
        }

        return $this->getData(PaymentMethods::class, [
            'currentCoreVersion' => ['major' => $coreMajorVersion],
            'shopCountryCode'    => $counryCode,
        ]);
    }

    /**
     * @return array
     */
    public function getAllGDPRModules(): array
    {
        $coreMajorVersion = '';

        $coreVersion = $this->coreConfigDataSource->version;
        if (preg_match('/(\d+\.\d+)\.(\d+)\.(\d+)/', $coreVersion, $matches)) {
            $coreMajorVersion = $matches[1];
        }

        return $this->getData(GDPRModules::class, [
            'core_major_version' => $coreMajorVersion,
        ]);
    }

    /**
     * @return array
     */
    public function getAllShippingMethods(): array
    {
        $coreMajorVersion = '';

        $coreVersion = $this->coreConfigDataSource->version;
        if (preg_match('/(\d+\.\d+)\.(\d+)\.(\d+)/', $coreVersion, $matches)) {
            $coreMajorVersion = $matches[1];
        }

        return $this->getData(ShippingMethods::class, [
            'core_major_version' => $coreMajorVersion,
        ]);

    }

    /**
     * @param array $actions
     *
     * @return array
     */
    public function getDataSet(array $actions): array
    {
        $data = [];

        foreach ($actions as $k => $actionData) {
            if (!is_array($actionData)) {
                $data[$actionData] = [0];
            } else {
                $data[$k] = $actionData;
            }
        }

        $result = $this->getData(Set::class, $data);

        if (!$result) {
            return array_map(static function () {
                return [];
            }, $data);
        }

        return $result;
    }

    /**
     * @param string $identity
     * @param string $version
     * @param array  $state
     * @param bool   $compressed
     *
     * @return RangeIterator
     */
    public function getPackIterator($identity, $version, $state = [], $compressed = false): RangeIterator
    {
        if ($identity !== 'CDev-Core') {
            [$author, $name] = explode('-', $identity);

            $keyInfo = $this->licenseDataSource->findBy([
                'author' => $author,
                'name'   => $name,
            ]);

            $data = [
                Constant::FIELD_MODULE_ID => $this->getModuleId($author, $name, $version),
                Constant::FIELD_KEY       => $keyInfo ? $keyInfo['keyValue'] : null,
                Constant::FIELD_GZIPPED   => $compressed,
            ];

            return $this->getMarketplace()->getRangeIterator(
                Constant::REQUEST_ADDON_PACK,
                $data,
                $state
            );
        }

        [$supreme, $major, $minor, $build] = explode('.', $version);

        return $this->getMarketplace()->getRangeIterator(
            Constant::REQUEST_CORE_PACK,
            [
                Constant::FIELD_VERSION => [
                    Constant::FIELD_VERSION_MAJOR => "{$supreme}.{$major}",
                    Constant::FIELD_VERSION_MINOR => $minor,
                    Constant::FIELD_VERSION_BUILD => $build ?: 0,
                ],
                Constant::FIELD_GZIPPED => $compressed,
            ],
            $state
        );
    }

    /**
     * @param string $identity
     * @param string $version
     *
     * @return array
     */
    public function getHashes($identity, $version): array
    {
        if ($identity !== 'CDev-Core') {
            [$author, $name] = explode('-', $identity);

            $keyInfo = $this->licenseDataSource->findBy([
                'author' => $author,
                'name'   => $name,
            ]);

            return $this->getData(AddonHash::class, [
                Constant::FIELD_MODULE_ID => $this->getModuleId($author, $name, $version),
                Constant::FIELD_KEY       => $keyInfo['keyValue'] ?? null,
                Constant::FIELD_GZIPPED   => false,
            ]);
        }

        [, $major, $minor, $build] = explode('.', $version);

        $versionParts = [
            Constant::FIELD_VERSION_MAJOR => "5.{$major}",
            Constant::FIELD_VERSION_MINOR => $minor,
            Constant::FIELD_VERSION_BUILD => $build ?: 0,
        ];

        return $this->getData(CoreHash::class, [
            Constant::FIELD_VERSION => $versionParts,
            Constant::FIELD_GZIPPED => false,
        ]);
    }

    /**
     * @param array $modules
     *
     * @return array
     */
    public function getHashesBatch($modules): array
    {
        $data            = [];
        $modulesIdentity = [];
        foreach ($modules as ['id' => $id, 'version' => $version]) {
            [$author, $name] = explode('-', $id);
            $moduleKey = $this->getModuleId($author, $name, $version);

            $data[]                      = $moduleKey;
            $modulesIdentity[$moduleKey] = $id;
        }

        $response = $this->getData(AddonHashBatch::class, ['moduleId' => $data]);

        $result = [];
        foreach ($response as $key => $hashes) {
            $result[$modulesIdentity[$key]] = $hashes;
        }

        return $result;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function getVersionInfo($data): array
    {
        return $this->getData(VersionInfo::class, ['entities' => $data]);
    }

    /**
     * @param string   $key
     * @param int|null $wave
     *
     * @return array
     * @throws Exception
     */
    public function registerLicenseKey($key, $wave = null): array
    {
        $data = [
            Constant::FIELD_KEY         => $key,
            Constant::FIELD_DO_REGISTER => 1,
        ];

        if ($wave) {
            $data[Constant::FIELD_WAVE] = $wave;
        }

        $result = $this->getData(CheckAddonKey::class, $data);

        if ($this->getLastError()) {
            throw $this->getLastError();
        }

        return $result;
    }

    /**
     * @param string $token
     *
     * @return array
     */
    public function getTokenData($token): array
    {
        return $this->getData(GetTokenData::class, [
            Constant::FIELD_TOKEN => $token,
        ]);
    }

    /**
     * @param string $email
     *
     * @return bool
     * @throws Exception
     */
    public function resendLicenseKey($email): bool
    {
        $result = (bool) $this->getData(ResendKey::class, [
            Constant::FIELD_EMAIL => $email,
        ]);

        if ($this->getLastError()) {
            throw $this->getLastError();
        }

        return $result;
    }

    /**
     * @param string|string[] $key
     *
     * @return array
     */
    public function getLicenseInfo($key): array
    {
        return $this->getData(CheckAddonKey::class, [
            Constant::FIELD_KEY => is_string($key) ? trim($key) : array_unique(array_map('trim', $key)),
        ]);
    }

    /**
     * @param Module $module
     *
     * @return array
     */
    public function getModuleInfo(Module $module): array
    {
        $moduleId = $this->getModuleId(
            $module->author,
            $module->name,
            $module->version
        );

        return $this->getData(Marketplace\Request\AddonInfo::class, [
            Constant::FIELD_MODULE_ID => $moduleId,
        ]);
    }

    /**
     * @param array  $keys
     * @param string $wave
     *
     * @return array
     */
    public function setKeyWave($keys, $wave): array
    {
        return $this->getData(SetKeyWave::class, [
            'keys' => $keys,
            'wave' => $wave,
        ]);
    }

    /**
     * @param string   $email
     * @param Module[] $modules
     *
     * @return array
     */
    public function requestForUpgrade($email, $modules): array
    {
        $modulesField = [];
        foreach ($modules as $module) {
            if ($module->version || $module->installedVersion) {
                $version = $module->installedVersion ?: $module->version;
                [$system, $major, $minor, $build] = Module::explodeVersion($version);

                $modulesField[] = [
                    'name'   => $module->name,
                    'author' => $module->author,
                    'major'  => $system . '.' . $major,
                    'minor'  => $minor . ($build > 0 ? ('.' . $build) : ''),
                ];
            }
        }

        if ($modulesField) {
            return $this->getData(OutdatedModule::class, [
                'email'   => $email,
                'modules' => $modulesField,
            ]);
        }

        return [];
    }

    /**
     * @return Exception|null
     */
    public function getLastError(): ?Exception
    {
        return $this->lastError;
    }

    /**
     * @param Exception|null $lastError
     */
    public function setLastError(?Exception $lastError): void
    {
        $this->lastError = $lastError;
    }

    /**
     * Get sections for main page
     *
     * @return array
     */
    public function getSections()
    {
        return $this->getData(Marketplace\Request\Sections::class);
    }

    /**
     * @return bool
     */
    public function isMarketplaceLocked(): bool
    {
        $expiration = $this->coreConfigDataSource->find('marketplaceLockExpiration');

        return $expiration && time() < (int) $expiration;
    }

    /**
     * @param string $requestName
     * @param array  $params
     *
     * @return array
     */
    private function getData($requestName, $params = []): array
    {
        $this->setLastError(null);

        if ($this->isMarketplaceLocked()) {
            $this->logger->warning('Marketplace is locked', [
                'requestName' => $requestName,
            ]);

            return [];
        }

        try {
            return (array) $this->getMarketplace()->getData($requestName, $params);
        } catch (TransportException $e) {
            $this->lockMarketplace();

            $this->setLastError($e);

            $this->logger->warning('Set marketplace lock', [
                'request' => $requestName,
                'code'    => $e->getCode(),
                'message' => $e->getMessage(),
            ]);

        } catch (MarketplaceException $e) {
            $this->setLastError($e);

            if ($e->getCode() !== 0) {
                $this->logger->critical($e->getMessage(), $e->getData()); // todo: check exception message and data
            }
        }

        return [];
    }

    private function lockMarketplace(): void
    {
        $this->coreConfigDataSource->saveOne(time() + 3600, 'marketplaceLockExpiration');
    }

    private function unlockMarketplace(): void
    {
        $this->coreConfigDataSource->saveOne(null, 'marketplaceLockExpiration');
    }

    /**
     * @return Marketplace
     */
    private function getMarketplace(): Marketplace
    {
        if ($this->marketplace === null) {
            $this->marketplace = new Marketplace($this->getConfigData($this->app['xc_config'], $this->app['config']));
        }

        return $this->marketplace;
    }

    /**
     * Common data for all request types
     *
     * @param string[]   $config
     * @param string[][] $xcConfig
     *
     * @return array
     */
    private function getConfigData($xcConfig, $config): array
    {
        $commonParams = [
            Constant::FIELD_VERSION_API => Constant::MP_API_VERSION,
        ];

        $authCode  = $xcConfig['installer_details']['auth_code'];
        $secretKey = $xcConfig['installer_details']['shared_secret_key'];

        $commonParams[Constant::FIELD_SHOP_ID]     = $authCode ? md5($authCode . $secretKey) : '';
        $commonParams[Constant::FIELD_SHOP_DOMAIN] = $xcConfig['host_details']['http_host'];
        $commonParams[Constant::FIELD_SHOP_URL]    = $config['scheme']
            . '://'
            . $xcConfig['host_details']['http_host']
            . $xcConfig['host_details']['web_dir'];

        $coreVersion = $this->coreConfigDataSource->version;
        if (preg_match('/(\d+\.\d+)\.(\d+)\.(\d+)/', $coreVersion, $matches)) {
            $commonParams[Constant::FIELD_VERSION_CORE_CURRENT] = [
                Constant::FIELD_VERSION_MAJOR => $matches[1],
                Constant::FIELD_VERSION_MINOR => $matches[2],
                Constant::FIELD_VERSION_BUILD => $matches[3],
            ];
        }

        $coreLicense = $this->licenseDataSource->findBy([
            'author' => 'CDev',
            'name'   => 'Core',
        ]);
        if ($coreLicense) {
            $commonParams[Constant::FIELD_XCN_LICENSE_KEY] = $coreLicense['keyValue'];
        }

        $commonParams[Constant::FIELD_INSTALLATION_LNG] = $xcConfig['installation']['installation_lng'];

        $commonParams[Constant::FIELD_SHOP_COUNTRY_CODE] = $this->coreConfigDataSource->shopCountryCode ?? 'US';

        if (isset($config['affiliate_id'])) {
            $commonParams[Constant::FIELD_AFFILIATE_ID] = $config['affiliate_id'];
        }

        if ($this->isCloud) {
            $commonParams['cloud'] = 1;
        }

        return [
            'endpoint'      => $xcConfig['marketplace']['url'],
            'logger'        => $this->logger,
            'common_params' => $commonParams,
        ];
    }

    /**
     * @param string $author
     * @param string $name
     * @param string $version
     *
     * @return string
     */
    private function getModuleId($author, $name, $version): string
    {
        [$core, $major, $minor, $build] = explode('.', $version);

        $version = (int) $build === 0
            ? ($core . '.' . $major . '.' . $minor)
            : ($core . '.' . $major . '.' . $minor . '.' . $build);

        return md5("{$author}.{$name}.{$version}");
    }

    /**
     * @return array
     */
    private function getModuleLicenseKeys()
    {
        $result = [];

        foreach ($this->licenseDataSource->getAll() as $licenseKey) {
            if ('CDev' === $licenseKey['author'] && 'Core' === $licenseKey['name']) {
                continue;
            }

            $result[] = $licenseKey['keyValue'];
        }

        return $result;
    }

    /**
     * @param string $user_id
     *
     * @return string
     */
    public function getIntercomHash(string $user_id): string
    {
        $result = null;
        $guzzle = new Marketplace\Transport\Guzzle([
            Marketplace\Transport\AHTTP::ENDPOINT => $this->app['config']['marketplace.segment_url'],
        ]);

        try {
            $response = $guzzle->doAPIRequest('intercom_hash.php', ['user_id' => $user_id]);
            $result = $response['body'];
        } catch (TransportException $exception) {
        }

        return $result;
    }

    /**
     * @return string
     */
    public function getLicenseType()
    {
        $licenseType  = 'Trial';

        foreach ($this->licenseDataSource->getAll() as $licenseKey) {
            if ('CDev' === $licenseKey['author'] && 'Core' === $licenseKey['name']) {
                if (($keyData = $licenseKey['keyData'])
                    && !empty($keyData['editionName'])
                ) {
                    $licenseType = $keyData['editionName'];
                }
            }
        }

        return $licenseType;
    }

    /**
     * @return array
     */
    public function getEditions(): array
    {
        return $this->getData(Editions::class);
    }
}
