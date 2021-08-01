<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\GoSocial\Model\Repo;


class Category extends \XLite\Model\Repo\Category implements \XLite\Base\IDecorator
{
    /**
     * @param array $record
     * @param array $regular
     *
     * @return array
     */
    protected function assembleRegularFieldsFromRecord(array $record, array $regular = [])
    {
        if (isset($record['ogMeta'])) {
            $record['ogMeta'] = \XLite\Module\CDev\GoSocial\Logic\OgMeta::prepareOgMeta($record['ogMeta']);
        }

        return parent::assembleRegularFieldsFromRecord($record, $regular);
    }
}