<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Query\Data;

use XCart\Marketplace\Constant;
use XCart\Marketplace\Request\Notifications;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service()
 */
class RecommendedModulesDataSource extends AMarketplaceDataSetSource
{
    const TYPE_MODULE = 'module';

    /**
     * @return string
     */
    protected function getRequest(): string
    {
        return Notifications::class;
    }

    /**
     * @return array
     */
    public function getAll(): array
    {
        $data = parent::getAll();
        $data = array_filter($data, static function ($item) {
            return $item['type'] === static::TYPE_MODULE;
        });

        return $data;
    }

    /**
     * @param string $pageParams
     *
     * @return array
     */
    public function findByPageParams(string $pageParams): array
    {
        $modules = $this->getAll();
        $pageParams = json_decode($pageParams, true);

        return array_filter($modules, static function ($module) use ($pageParams) {
            foreach ($module[Constant::FIELD_NOTIFICATION_PAGE_PARAMS] as $modulePageParam) {
                $moduleParamKey   = $modulePageParam[Constant::FIELD_NOTIFICATION_PARAM_KEY];
                $moduleParamValue = $modulePageParam[Constant::FIELD_NOTIFICATION_PARAM_VALUE];

                if ($moduleParamValue !== $pageParams[$moduleParamKey]) {
                    return false;
                }
            }

            return true;
        });
    }
}