<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Decorator\Plugin\Templates\Plugin\ViewListsPostprocess;

use Includes\Decorator\Utils\CacheManager;
use XLite\Core\Database;
use XLite\Model\ViewList;

/**
 * Main
 */
class Main extends \Includes\Decorator\Plugin\Templates\Plugin\APlugin
{
    /**
     * Check - current plugin is bocking or not
     *
     * @return boolean
     */
    public function isBlockingPlugin()
    {
        return $this->getVersionKey();
    }

    /**
     * Execute certain hook handler
     *
     * @return void
     */
    public function executeHookHandler()
    {
        // Delete old and rename new
        if ($this->getVersionKey()) {
            ViewList::setVersionKey(null);

            $repo = Database::getRepo('XLite\Model\ViewList');
            $key  = $this->getVersionKey();

            $this->restoreOverriddenRecords($key);

            $repo->deleteObsolete($key);
            $repo->markCurrentVersion($key);

            $this->deleteVersionKey();
        }
    }

    /**
     * Restores overridden records
     *
     * @param $currentKey
     */
    public function restoreOverriddenRecords($currentKey)
    {
        $repo = \XLite\Core\Database::getRepo('XLite\Model\ViewList');
        $overrides = $repo->findOverridden();

        if ($overrides) {
            /** @var \XLite\Model\ViewList $override */
            foreach ($overrides as $override) {
                $entity = $repo->findEqual($override, true);

                if ($entity) {
                    $entity->mapOverrides($override);
                } else {
                    $entity = $override->cloneEntity();
                    $entity->setVersion($currentKey);
                    $entity->setDeleted(!$entity->isViewListModuleEnabled());

                    $equalParent = $repo->findEqualParent($override->getParent() ?: $entity, true);

                    if ($equalParent) {
                        $entity->setParent($equalParent);
                    }

                    \XLite\Core\Database::getEM()->persist($entity);
                }
            }

            \XLite\Core\Database::getEM()->flush();
        }
    }
}
