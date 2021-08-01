<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\FileAttachments\Model\Repo\Base;

/**
 * Abstract storage repository
 */
abstract class Storage extends \XLite\Module\XC\CanadaPost\Model\Repo\Base\Storage implements \XLite\Base\IDecorator
{
    /**
     * Define all storage-based repositories classes list
     *
     * @return array
     */
    protected function defineStorageRepositories()
    {
        $list = parent::defineStorageRepositories();

        $list[] = 'XLite\Module\CDev\FileAttachments\Model\Product\Attachment\Storage';

        return $list;
    }
}
