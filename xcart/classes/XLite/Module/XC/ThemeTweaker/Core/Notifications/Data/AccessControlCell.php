<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\Core\Notifications\Data;


use XLite\Core\Cache\ExecuteCachedTrait;
use XLite\Core\Database;

class AccessControlCell extends Provider
{
    use ExecuteCachedTrait;

    public function getData($templateDir)
    {
        return $this->getAccessControlCell();
    }

    public function getName($templateDir)
    {
        return 'access control cell';
    }

    public function validate($templateDir, $hash)
    {
        return [];
    }

    public function isAvailable($templateDir)
    {
        return !!$this->getData($templateDir);
    }

    protected function getTemplateDirectories()
    {
        return [
            'access_link'
        ];
    }

    /**
     * @param string $templateDir
     *
     * @return \XLite\Model\AccessControlCell|null
     */
    protected function getAccessControlCell()
    {
        return $this->executeCachedRuntime(function () {
            $lastAccessControlCell = Database::getRepo('XLite\Model\AccessControlCell')->findLast();
            return $lastAccessControlCell ?? Database::getRepo('XLite\Model\AccessControlCell')->generateBaseAccessControlCell();
        });
    }
}