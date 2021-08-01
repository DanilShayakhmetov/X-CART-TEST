<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\Core\Request\Store;

use XLite\Core\Database;
use XLite\Module\XC\MailChimp\Core\Request\MailChimpRequest;
use XLite\Module\XC\MailChimp\Model\MailChimpList;
use XLite\Module\XC\MailChimp\Model\Store;

class Create extends MailChimpRequest
{
    /**
     * @param array $storeData
     */
    public function __construct($storeData)
    {
        parent::__construct('Creating store', 'post', 'ecommerce/stores', $storeData);
    }

    /**
     * @param array $storeData
     *
     * @return self
     */
    public static function getRequest($storeData): self
    {
        return new self($storeData);
    }

    /**
     * @param array $storeData
     *
     * @return mixed
     */
    public static function executeAction($storeData)
    {
        return self::getRequest($storeData)->execute();
    }

    /**
     * @param array $storeData
     *
     * @return bool
     */
    public static function createStoreInDB($storeData): bool
    {
        if (!self::canCreateInDB($storeData)) {
            return false;
        }

        $repo = Database::getRepo(Store::class);

        $duplicateByList = $repo->findByList($storeData['list_id']);
        if ($duplicateByList) {
            $repo->deleteInBatch($duplicateByList);
        }

        try {
            $list = Database::getEM()->getReference(MailChimpList::class, $storeData['list_id']);

            $store = new Store();
            $store->setId($storeData['id']);
            $store->setName($storeData['name']);
            $store->setList($list);
            $store->setMain($storeData['is_main'] ?? false);

            $store->create();
        } catch (\Exception $e) {
        }

        return true;
    }

    /**
     * @param array $storeData
     *
     * @return bool
     */
    protected static function canCreateInDB($storeData): bool
    {
        return isset($storeData['list_id'], $storeData['id'], $storeData['name']);
    }

    /**
     * @return array|null
     */
    public function execute(): ?array
    {
        $storeData = $this->getArgs();

        if (!Check::executeAction($storeData['id'])) {
            $result = parent::execute();

            Check::dropActionCache($storeData['id']);

            if ($result && !Check::isStoreExistsInDB($storeData['id'])) {
                self::createStoreInDB($storeData);
            }

            return $result;
        }

        return null;
    }
}
