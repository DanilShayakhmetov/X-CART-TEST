<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\CloudSearch\Controller;

use Doctrine\DBAL\Logging\DebugStack;
use XLite\Core\Config;
use XLite\Core\Database;
use XLite\Core\Request;
use XLite\Module\QSL\CloudSearch\Core\ServiceApiClient;
use XLite\Module\QSL\CloudSearch\Core\StoreApi;

/**
 * CloudSearch API implementation trait
 */
trait ApiControllerTrait
{
    protected $debugStack;

    /**
     * 'info' api verb
     *
     * @return void
     */
    protected function doActionInfo()
    {
        $api = StoreApi::getInstance();

        $data = $api->getApiSummary();

        $this->printJSONAndExit($data);
    }

    /**
     * 'profile' api verb
     *
     * @return void
     */
    protected function doActionProfile()
    {
        $client = new ServiceApiClient();

        if (Request::getInstance()->key !== $client->getSecretKey()) {
            header('HTTP/1.0 403 Forbidden', true, 403);
            die;
        }

        $config = Config::getInstance();

        $accountNames = [];
        $emails = @unserialize($config->Company->site_administrator);

        if (!$emails) {
            $emails = $config->Company->site_administrator ? [$config->Company->site_administrator] : [];
        }

        $profileRepo = Database::getRepo('XLite\Model\Profile');

        foreach ($emails as $email) {
            $profile = $profileRepo->findByLogin($email);

            $accountNames[] = $profile ? $profile->getName() : null;
        }

        $data = [
            'company_name'  => $config->Company->company_name,
            'emails'        => $emails,
            'account_names' => $accountNames,
            'timezone'      => $config->Units->time_zone,
        ];

        $this->printJSONAndExit($data);
    }

    /**
     * 'products' api verb
     *
     * @return void
     */
    protected function doActionProducts()
    {
        $measureSqlQueries = LC_DEVELOPER_MODE;

        if ($measureSqlQueries) {
            $this->startMeasuring();
        }

        $api = StoreApi::getInstance();

        $data = $api->getProducts($this->getConditionParams());

        if ($measureSqlQueries) {
            $this->stopMeasuring();
        }

        $this->printJSONAndExit($data);
    }

    /**
     * 'categories' api verb
     *
     * @return void
     */
    protected function doActionCategories()
    {
        $api = StoreApi::getInstance();

        $data = $api->getCategories($this->getConditionParams());

        $this->printJSONAndExit($data);
    }

    /**
     * 'pages' api verb
     *
     * @return void
     */
    protected function doActionPages()
    {
        $api = StoreApi::getInstance();

        list($start, $limit) = $this->getLimits();

        $data = $api->getPages($start, $limit);

        $this->printJSONAndExit($data);
    }

    protected function doActionManufacturers()
    {
        $api = StoreApi::getInstance();

        $data = $api->getBrands();

        $this->printJSONAndExit($data);
    }

    /**
     * Stores new secret key sent from CloudSearch server
     *
     * @return void
     */
    protected function doActionFinalizeRegistration()
    {
        $request = Request::getInstance();

        $shopKey = Database::getRepo('XLite\Model\TmpVar')->getVar('cloud_search_shop_key');

        if (empty($request->shopKey) || empty($request->key) || $shopKey !== $request->shopKey) {
            header('HTTP/1.0 403 Forbidden', true, 403);
            die;
        }

        $repo = Database::getRepo('XLite\Model\Config');

        $secretKeySetting = $repo->findOneBy([
            'name'     => 'secret_key',
            'category' => 'QSL\CloudSearch',
        ]);

        $secretKeySetting->setValue($request->key);

        Database::getEM()->flush();

        $this->printJSONAndExit([]);
    }

    /**
     * Set plan features via API
     *
     * @return void
     */
    protected function doActionSetPlanFeatures()
    {
        $request = Request::getInstance();

        $apiClient = new ServiceApiClient();

        $secretKey = $apiClient->getSecretKey();

        if ($secretKey && $secretKey === $request->secret_key) {
            $repo = Database::getRepo('XLite\Model\Config');

            $planFeatures = $repo->findOneBy([
                'name'     => 'planFeatures',
                'category' => 'QSL\CloudSearch',
            ]);

            if ($planFeatures) {
                $planFeatures->setValue(json_encode($request->features));

                Database::getEM()->flush();
            }
        }

        $this->printNoneAndExit();
    }

    /**
     * Change store settings via API
     *
     * @return void
     */
    protected function doActionSetSettings()
    {
        $request = Request::getInstance();

        $apiClient = new ServiceApiClient();

        $secretKey = $apiClient->getSecretKey();

        if ($secretKey && $secretKey === $request->secret_key) {
            $settings = $request->settings;

            if (isset($settings['isCloudFiltersEnabled'])) {
                $this->setSetting('isCloudFiltersEnabled', (bool)$settings['isCloudFiltersEnabled']);
            }

            if (isset($settings['isAdminSearchEnabled'])) {
                $this->setSetting('isAdminSearchEnabled', (bool)$settings['isAdminSearchEnabled']);
            }
        }

        $this->printNoneAndExit();
    }

    protected function setSetting($name, $value)
    {
        $repo = Database::getRepo('XLite\Model\Config');

        $setting = $repo->findOneBy([
            'name'     => $name,
            'category' => 'QSL\CloudSearch',
        ]);

        $setting->setValue($value);

        Database::getEM()->flush($setting);
    }

    protected function printJSONAndExit($data)
    {
        header('Content-type: application/json');

        echo json_encode($data);

        exit;
    }

    protected function printNoneAndExit()
    {
        $this->silent = true;
        $this->setSuppressOutput(true);
    }

    /**
     * @return array
     */
    protected function getConditionParams()
    {
        $params = [];

        $request = Request::getInstance();

        if (isset($request->start, $request->limit)) {
            $params['start'] = max(0, $request->start);
            $params['limit'] = max(1, $request->limit ?: StoreApi::MAX_ENTITIES_AT_ONCE);
        }

        if (isset($request->ids)) {
            $params['ids'] = $request->ids;
        }

        return $params;
    }

    /**
     * Get adjusted request limits
     *
     * @return array
     */
    protected function getLimits()
    {
        $request = Request::getInstance();

        $start = max(0, $request->start);
        $limit = max(1, $request->limit ?: StoreApi::MAX_ENTITIES_AT_ONCE);

        return [$start, $limit];
    }

    /**
     * Start measuring SQL queries
     */
    protected function startMeasuring()
    {
        $em = Database::getEM();

        $this->debugStack = new DebugStack();

        $em->getConfiguration()->setSQLLogger($this->debugStack);
    }

    /**
     * Stop measuring SQL queries
     */
    protected function stopMeasuring()
    {
        header('X-SQL-Queries: ' . count($this->debugStack->queries));
    }
}
