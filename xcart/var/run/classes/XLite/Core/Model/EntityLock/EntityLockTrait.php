<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Model\EntityLock;

use XLite\Core\Database;

trait EntityLockTrait
{
    /**
     * @param string $type
     *
     * @return integer
     */
    abstract protected function getLockTTL($type = 'lock');

    /**
     * @param string       $type
     * @param integer|null $ttl
     */
    public function setEntityLock($type = 'lock', $ttl = null)
    {
        Database::getCacheDriver()->save(
            $this->getLockIdentifier($type),
            \LC_START_TIME + ($ttl ?: $this->getLockTTL($type))
        );
    }

    /**
     * @param string $type
     *
     * @return boolean
     */
    public function isEntityLocked($type = 'lock')
    {
        return (int) Database::getCacheDriver()->fetch($this->getLockIdentifier($type)) > 0;
    }

    /**
     * @param string $type
     *
     * @return boolean
     */
    public function isEntityLockExpired($type = 'lock')
    {
        $lockExpiration = (int) Database::getCacheDriver()->fetch($this->getLockIdentifier($type));

        return $lockExpiration > 0 && $lockExpiration < \LC_START_TIME;
    }

    /**
     * @param string $type
     */
    public function unsetEntityLock($type = 'lock')
    {
        Database::getCacheDriver()->delete($this->getLockIdentifier($type));
    }

    /**
     * @param mixed|null $identifierData
     *
     * @return string
     * @throws \Exception
     */
    protected function getLockIdentifier($identifierData = null)
    {
        return 'EntityLock_'
            . $this->getEntityName()
            . $this->getUniqueIdentifier()
            . ($identifierData ? ('-' . md5(serialize($identifierData))) : '');
    }
}
