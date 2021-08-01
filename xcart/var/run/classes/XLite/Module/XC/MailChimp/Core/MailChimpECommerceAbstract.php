<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\Core;

use XLite\Module\XC\MailChimp\Core\Request\Campaign\Get;
use XLite\Module\XC\MailChimp\Core\Request\IMailChimpRequest;
use XLite\Module\XC\MailChimp\Core\Request\MailChimpRequest;
use XLite\Module\XC\MailChimp\Core\Request\Store as MailChimpStore;
use XLite\Module\XC\MailChimp\Logic\DataMapper\Product;
use XLite\Module\XC\MailChimp\Model;

require_once LC_DIR_MODULES . 'XC' . LC_DS . 'MailChimp' . LC_DS . 'lib' . LC_DS . 'MailChimp.php';

/**
 * MailChimp core class
 */
abstract class MailChimpECommerceAbstract extends \XLite\Base\Singleton
{
    use \XLite\Core\Cache\ExecuteCachedTrait;

    /**
     * @var array
     */
    protected $existingStores = [];

    /**
     * @var \DrewM\MailChimp\MailChimp
     */
    protected $mailChimpAPI;

    /**
     * Protected constructor.
     * It's not possible to instantiate a derived class (using the "new" operator)
     * until that child class is not implemented public constructor
     *
     * @throws MailChimpException
     */
    protected function __construct()
    {
        parent::__construct();

        try {
            $this->mailChimpAPI = new \XLite\Module\XC\MailChimp\Core\MailChimpLoggableAPI(
                \XLite\Core\Config::getInstance()->XC->MailChimp->mailChimpAPIKey
            );

        } catch (\Exception $e) {
            if (
                MailChimpException::MAILCHIMP_NO_API_KEY_ERROR === $e->getMessage()
                && \XLite::isAdminZone()
            ) {
                \XLite\Core\TopMessage::addError($e->getMessage());

                \XLite\Core\Operator::redirect(
                    \XLite\Core\Converter::buildURL('mailchimp_options')
                );
            }

            throw new MailChimpException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param IMailChimpRequest $request
     *
     * @return array|null
     */
    public function executeRequest(IMailChimpRequest $request): ?array
    {
        $this->mailChimpAPI->setActionMessageToLog($request->getName());

        $result = $this->mailChimpAPI->{$request->getMethod()}($request->getAction(), $request->getArgs(), $request->getTimeout());

        return $this->mailChimpAPI->success()
            ? (is_array($result) ? $result : [])
            : null;
    }

    /**
     * Create new store
     *
     * @param array       $storeData
     * @param string $listId
     *
     * @return array|null
     */
    public function createStore($storeData, $listId): ?array
    {
        $data = array_merge(
            $this->getCommonStoreData(),
            [
                'id'            => $storeData['store_id'],
                'name'          => $storeData['store_name'],
                'list_id'       => $listId,
                'currency_code' => $storeData['currency_code'],
                'money_format'  => $storeData['money_format'],
            ]
        );

        $request = new MailChimpRequest('Creating store', 'post', "ecommerce/stores", $data);
        $result  = $request->execute();

        if ($result
            && !\XLite\Core\Database::getRepo('XLite\Module\XC\MailChimp\Model\Store')->find($storeData['store_id'])
        ) {
            $this->createStoreReference(
                $listId,
                $storeData['store_id'],
                $storeData['store_name'],
                isset($storeData['is_main']) ? $storeData['is_main'] : false
            );
        }

        MailChimpStore\Check::dropActionCache($storeData['store_id']);

        $this->existingStores[$storeData['store_id']] = !!$result;

        return $result;
    }

    /**
     * @param string $storeId
     * @param bool   $syncingStatus
     *
     * @return array|null
     */
    public function changeStoreSyncingStatus($storeId, $syncingStatus): ?array
    {
        return $this->updateStoreData($storeId, ['is_syncing' => $syncingStatus]);
    }

    /**
     * @param string $listId Can't be empty
     * @param bool   $selected
     */
    public function updateStoreAndReference($listId, $selected): void
    {
        $storeId = MailChimpStore\Get::getStoreIdByDB($listId);
        $storeName = MailChimpStore\Get::getStoreNameByDB($listId);

        if ($selected) {
            $storeExists = MailChimpStore\Check::executeAction($storeId);
            if (!$storeExists) {
                $this->createStore(
                    [
                        'campaign_id'   => '',
                        'store_id'      => $storeId,
                        'store_name'    => $storeName,
                        'currency_code' => \XLite::getInstance()->getCurrency()->getCode(),
                        'money_format'  => \XLite::getInstance()->getCurrency()->getPrefix()
                            ?: \XLite::getInstance()->getCurrency()->getSuffix(),
                        'is_main'       => $selected,
                    ],
                    $listId
                );
            } else {
                $this->updateStoreData($storeId);
            }
        }

        /** @var \XLite\Module\XC\MailChimp\Model\Store $existingStore */
        $existingStore = \XLite\Core\Database::getRepo('XLite\Module\XC\MailChimp\Model\Store')->find($storeId);

        if ($existingStore) {
            $existingStore->setMain($selected);
            $existingStore->setName($storeName);
        } else {
            $this->createStoreReference(
                $listId,
                $storeId,
                $storeName,
                $selected
            );
        }
    }

    /**
     * @param string     $storeId
     * @param array|null $data
     *
     * @return array|null
     */
    public function updateStoreData($storeId, $data = null): ?array
    {
        if (!$data) {
            $data = $this->getCommonStoreData();
        }

        $request = new MailChimpRequest('Changing store data', 'patch', "ecommerce/stores/{$storeId}", $data);

        return $request->execute();
    }

    /**
     * @return array
     */
    public function getCommonStoreData(): array
    {
        $timezone = '';

        try {
            $timezoneObj = \XLite\Core\Converter::getTimeZone();
            if ($timezoneObj) {
                $timezone = $timezoneObj->getName();
            }
        } catch (\Exception $e) {
        }

        return [
            'name'           => \XLite\Core\Config::getInstance()->Company->company_name,
            'platform'       => 'X-Cart',
            'domain'         => \XLite\Core\URLManager::getShopURL(),
            'email_address'  => \XLite\Core\Mailer::getUsersDepartmentMail(),
            'primary_locale' => \XLite\Core\Config::getInstance()->General->default_language,
            'timezone'       => $timezone,
            'phone'          => \XLite\Core\Config::getInstance()->Company->company_phone,
            'address'        => $this->getStoreAddress(),
        ];
    }

    /**
     * @return array
     */
    public function getStoreAddress(): array
    {
        $data = [
            "address1"    => \XLite\Core\Config::getInstance()->Company->location_address,
            "address2"    => '',
            "city"        => \XLite\Core\Config::getInstance()->Company->location_city,
            "postal_code" => \XLite\Core\Config::getInstance()->Company->location_zipcode,
        ];

        $country = \XLite\Core\Config::getInstance()->Company->locationCountry;
        if ($country && $country instanceof \XLite\Model\Country) {
            $data["country"]      = $country->getCountry();
            $data["country_code"] = $country->getCode3();
        }

        $state = \XLite\Core\Config::getInstance()->Company->locationState;
        if ($state && $state instanceof \XLite\Model\State) {
            $data["province"]      = $state->getState();
            $data["province_code"] = $state->getCode();
        }

        return array_filter($data);
    }

    /**
     * @param string $listId
     * @param string $storeId
     * @param string $storeName
     * @param bool   $isMain
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function createStoreReference($listId, $storeId, $storeName, $isMain = false): void
    {
        $repo            = \XLite\Core\Database::getRepo('\XLite\Module\XC\MailChimp\Model\Store');
        $duplicateByList = $repo->findByList($listId);
        if ($duplicateByList) {
            $repo->deleteInBatch($duplicateByList);
        }

        $list  = \XLite\Core\Database::getEM()->getReference(
            'XLite\Module\XC\MailChimp\Model\MailChimpList',
            $listId
        );
        $store = new Model\Store();
        $store->setId($storeId);
        $store->setName($storeName);
        $store->setList($list);
        $store->setMain($isMain);

        $store->create();
    }

    public function updateConnectedSites(): void
    {
        $storesRepo = \XLite\Core\Database::getRepo('XLite\Module\XC\MailChimp\Model\Store');

        $request = new MailChimpRequest('Getting conneted sites', 'get', 'connected-sites');
        $result  = $request->execute();

        foreach ($result['sites'] as $site) {
            $store = $storesRepo->find($site['store_id']);

            if ($store) {
                \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption(
                    [
                        'category' => 'XC\MailChimp',
                        'name'     => 'mcjs',
                        'value'    => $site['site_script']['fragment'],
                    ]
                );
                break;
            }
        }
    }

    /**
     * @param string $storeId
     * @param array  $ordersData
     *
     * @return array|null
     */
    public function createOrdersBatchFromMappedData($storeId, array $ordersData): ?array
    {
        $operations = [];

        foreach ($ordersData as $orderId => $orderData) {
            $operations[] = [
                'method' => 'post',
                'path'   => "ecommerce/stores/{$storeId}/orders",
                'body'   => json_encode($orderData),
            ];
        }

        if (!$operations) {
            return null;
        }

        $request = new MailChimpRequest('Creating orders batch', 'post', "batches", [
            'operations' => $operations,
            'fields'     => 'id',
        ]);

        return $request->execute();
    }
}
