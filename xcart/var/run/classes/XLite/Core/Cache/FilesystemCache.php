<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Cache;


class FilesystemCache extends \Doctrine\Common\Cache\FilesystemCache
{
    protected function doFetch($id)
    {
        return @parent::doFetch($id);
    }

    protected function doContains($id)
    {
        return @parent::doContains($id);
    }
}