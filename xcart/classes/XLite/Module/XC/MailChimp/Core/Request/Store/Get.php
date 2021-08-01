<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\Core\Request\Store;

use Includes\Utils\URLManager;
use XLite\Core\Cache\ExecuteCached;
use XLite\Core\Config;
use XLite\Core\Database;
use XLite\Module\XC\MailChimp\Core\Request\Audience as MailChimpAudience;
use XLite\Module\XC\MailChimp\Core\Request\Campaign as MailChimpCampaign;
use XLite\Module\XC\MailChimp\Core\Request\MailChimpRequest;
use XLite\Module\XC\MailChimp\Model\MailChimpList;
use XLite\Module\XC\MailChimp\Model\Store;

class Get extends MailChimpRequest
{
    /**
     * @param string $listId
     *
     * @return string
     */
    public static function getStoreNameByDB($listId): string
    {
        /** @var MailChimpList $list */
        $list = Database::getRepo(MailChimpList::class)->find($listId);

        return sprintf(
            '%s (%s)',
            Config::getInstance()->Company->company_name,
            $list ? $list->getName() : 'unknown'
        );
    }

    /**
     * @param string $listId
     *
     * @return string
     */
    public static function getStoreIdByDB($listId): string
    {
        if ($listId) {
            /** @var Store $store */
            $store = Database::getRepo(Store::class)->findOneByList($listId);
            if ($store) {
                return $store->getId();
            }
        }

        $rawId = URLManager::getShopURL();
        $rawId .= $listId ?: 'no_list_id';
        $rawId .= 'xc_5_weak_salt';

        return md5($rawId);
    }

    /**
     * @param string $campaignId
     *
     * @return string
     */
    public static function getStoreIdByCampaignId($campaignId): string
    {
        return self::getStoreIdByDB(
            MailChimpAudience\Get::getListIdByCampaignId($campaignId)
        );
    }

    /**
     * @return string|null
     */
    public static function getDefaultAutomationStoreIdByDB(): ?string
    {
        /** @var Store $store */
        $store = Database::getRepo(Store::class)->findOneByList(
            \XLite\Core\Config::getInstance()->XC->MailChimp->defaultAutomationListId
        );

        return $store ? $store->getId() : null;
    }

    /**
     * @param string|null $campaignId
     *
     * @return array
     */
    public static function getActiveStores($campaignId = null)
    {
        return ExecuteCached::executeCachedRuntime(static function () use ($campaignId) {
            $result = [];

            if ($defaultAutomationStoreId = self::getDefaultAutomationStoreIdByDB()) {
                $result[$defaultAutomationStoreId] = [
                    'listId'    => \XLite\Core\Config::getInstance()->XC->MailChimp->defaultAutomationListId,
                    'storeName' => self::getStoreNameByDB(\XLite\Core\Config::getInstance()->XC->MailChimp->defaultAutomationListId),
                ];
            }

            $campaignId = $campaignId ?: MailChimpCampaign\Get::getCampaignIdFromRequest();
            if ($campaignId) {
                $listId = MailChimpAudience\Get::getListIdByCampaignId($campaignId);

                if ($listId) {
                    $automationStoreId = self::getStoreIdByCampaignId($campaignId);

                    $result[$automationStoreId] = [
                        'listId'    => $listId,
                        'storeName' => self::getStoreNameByDB($listId),
                    ];
                }
            }

            return $result;
        }, [__METHOD__]);
    }
}
