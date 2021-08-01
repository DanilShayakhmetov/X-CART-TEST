<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Egoods\Controller\Admin;

use XLite\Core\Operator;

/**
 * Storage
 */
abstract class Storage extends \XLite\Controller\Admin\Storage implements \XLite\Base\IDecorator
{
    /**
     * Read storage
     *
     * @param \XLite\Model\Base\Storage $storage Storage
     *
     * @return void
     */
    protected function readStorage(\XLite\Model\Base\Storage $storage)
    {
        if ($storage instanceof \XLite\Module\CDev\Egoods\Model\Product\Attachment\Storage
            && $storage->canBeSigned()
            && !$storage->isFileAvailable()
        ) {
            Operator::redirect($storage->getSignedUrl());
            return;
        }

        parent::readStorage($storage);
    }
}

