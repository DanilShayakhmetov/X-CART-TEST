<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\Core\Request\Store;

use XLite\Core\Database;
use XLite\Module\XC\MailChimp\Core\Request\MailChimpRequest;
use XLite\Module\XC\MailChimp\Model\Store;

class Update extends MailChimpRequest
{
    /**
     * @var string
     */
    protected $storeId;

    /**
     * @param string $storeId
     * @param array  $storeData
     */
    public function __construct($storeId, $storeData)
    {
        parent::__construct('Updating store', 'patch', "ecommerce/stores/{$storeId}", $storeData);

        $this->storeId = $storeId;
    }

    /**
     * @param string $storeId
     * @param array  $storeData
     *
     * @return self
     */
    public static function getRequest($storeId, $storeData): self
    {
        return new self($storeId, $storeData);
    }

    /**
     * @param string $storeId
     * @param array  $storeData
     *
     * @return mixed
     */
    public static function scheduleAction($storeId, $storeData)
    {
        return self::getRequest($storeId, $storeData)->schedule();
    }

    /**
     * @param string $storeId
     * @param array  $storeData
     *
     * @return bool
     */
    public static function updateStoreInDB($storeId, $storeData): bool
    {
        if (!Check::isStoreExistsInDB($storeId)) {
            return Create::createStoreInDB($storeData);
        }

        if (!self::canUpdateInDB($storeData)) {
            return false;
        }

        $repo = Database::getRepo(Store::class);
        try {
            /** @var Store $store */
            $store = \XLite\Core\Database::getRepo(Store::class)->find($storeId);

            $store->setName($storeData['store_name']);
            $store->setMain($storeData['is_main'] ?? false);

            $store->update();
        } catch (\Exception $e) {
        }
    }

    /**
     * @param $storeData
     *
     * @return bool
     */
    protected static function canUpdateInDB($storeData): bool
    {
        return isset($storeData['store_name']);
    }

    /**
     * @return array|null
     */
    public function execute(): ?array
    {
        if (Check::executeAction($this->storeId)) {
            $result = parent::execute();

            self::updateStoreInDb($this->storeId, $this->getArgs());

            return $result;
        }
    }
}
