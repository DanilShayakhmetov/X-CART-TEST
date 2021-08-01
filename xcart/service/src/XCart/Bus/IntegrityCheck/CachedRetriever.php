<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\IntegrityCheck;

use XCart\Bus\Query\Data\IDataSource;


/**
 * Class CachedRetriever
 */
abstract class CachedRetriever
{
    /**
     * @return IDataSource
     */
    abstract protected function getCacheDataSource();

    /**
     * @param mixed    $id
     * @param callable $retrieveCallback
     *
     * @return mixed
     */
    public function retrieveCached($id, callable $retrieveCallback)
    {
        $source = $this->getCacheDataSource();
        $result = $source->find($id);

        if ($result === null) {
            $result = $retrieveCallback();
            $source->saveOne($result, $id);
        }

        return $result;
    }
}
