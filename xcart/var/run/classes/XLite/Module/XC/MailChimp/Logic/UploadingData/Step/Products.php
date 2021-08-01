<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\Logic\UploadingData\Step;

use XLite\Module\XC\MailChimp\Core\Request\Product as MailChimpProduct;
use XLite\Module\XC\MailChimp\Logic\DataMapper;

class Products extends AStep
{
    /**
     * Process model
     *
     * @param \XLite\Model\Product|\XLite\Model\AEntity $model Model
     *
     * @return void
     */
    protected function processModel(\XLite\Model\AEntity $model)
    {
        foreach ($this->getStores() as $storeId) {
            MailChimpProduct\Update::executeAction($storeId, DataMapper\Product::getDataByProduct($model));
        }
    }

    /**
     * Get repository
     *
     * @return \XLite\Model\Repo\ARepo
     */
    protected function getRepository()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Product');
    }

    /**
     * @param array $models
     *
     * @return mixed
     */
    protected function processBatch(array $models)
    {
    }
}