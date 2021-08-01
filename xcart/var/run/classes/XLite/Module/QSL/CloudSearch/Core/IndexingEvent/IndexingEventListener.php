<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\CloudSearch\Core\IndexingEvent;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Events;
use XLite;
use XLite\Core\Database;
use XLite\Model\Category;
use XLite\Model\CategoryProducts;
use XLite\Model\Image\Category\Image as CategoryImage;
use XLite\Model\Image\Product\Image as ProductImage;
use XLite\Model\Product;
use XLite\Module\QSL\CloudSearch\Core\ServiceApiClient;


class IndexingEventListener implements EventSubscriber
{
    protected static $events = [];

    protected static $importEvents = [];

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return array
     */
    public function getSubscribedEvents()
    {
        return [
            Events::postPersist,
            Events::preUpdate,
            Events::preRemove,
            Events::postFlush,
        ];
    }

    public function postPersist(LifecycleEventArgs $eventArgs)
    {
        $startTime = microtime(true);

        $instance = $eventArgs->getEntity();

        if ($instance instanceof IndexingEventTriggerInterface) {
            $ids = $instance->getCloudSearchEntityIds();

            if ($ids) {
                $action = $instance->getCloudSearchEventAction()
                    ?: IndexingEventTriggerInterface::INDEXING_EVENT_CREATED_ACTION;

                foreach ($ids as $id) {
                    $this->addEvent($id, $instance->getCloudSearchEntityType(), $action);
                }
            }
        }

        IndexingEventProfiler::getInstance()->addToTotalTime(microtime(true) - $startTime);
    }

    public function preUpdate(PreUpdateEventArgs $eventArgs)
    {
        $startTime = microtime(true);

        $instance = $eventArgs->getEntity();

        if ($instance instanceof IndexingEventTriggerInterface && $this->hasChanges($instance, $eventArgs)) {
            $ids = $instance->getCloudSearchEntityIds();

            if ($ids) {
                $action = $instance->getCloudSearchEventAction()
                    ?: IndexingEventTriggerInterface::INDEXING_EVENT_UPDATED_ACTION;

                foreach ($ids as $id) {
                    $this->addEvent($id, $instance->getCloudSearchEntityType(), $action);
                }
            }
        }

        IndexingEventProfiler::getInstance()->addToTotalTime(microtime(true) - $startTime);
    }

    protected function hasChanges($instance, PreUpdateEventArgs $eventArgs)
    {
        if ($instance instanceof Product
            && (count($instance->getMemberships()->getDeleteDiff())
                || count($instance->getMemberships()->getInsertDiff()))
        ) {
            return true;
        }

        $changeSet = array_filter($eventArgs->getEntityChangeSet(), function($c) {
            return !is_scalar($c[0]) || !is_scalar($c[1]) || $c[0] != $c[1];
        });

        if (empty($changeSet)
            || $instance instanceof CategoryImage
            || $instance instanceof ProductImage
            || ($instance instanceof Product
                && array_intersect($this->getProductModelTrackFields(), array_keys($changeSet)) === [])
            || ($instance instanceof Category
                && array_intersect($this->getCategoryModelTrackFields(), array_keys($changeSet)) === [])
            || ($instance instanceof CategoryProducts
                && isset($changeSet['orderbyInProduct']) && count($changeSet) === 1)
        ) {
            return false;
        }

        return true;
    }

    protected function getProductModelTrackFields()
    {
        return [
            'sku',
            'price',
            'enabled',
            'amount',
            'vendor',
            'salePriceValue',
            'participateSale',
            'inventoryEnabled',
        ];
    }

    protected function getCategoryModelTrackFields()
    {
        return [
            'enabled',
            'parent',
        ];
    }

    public function preRemove(LifecycleEventArgs $eventArgs)
    {
        $startTime = microtime(true);

        $instance = $eventArgs->getEntity();

        if ($instance instanceof IndexingEventTriggerInterface) {
            $ids = $instance->getCloudSearchEntityIds();

            if ($ids) {
                $action = $instance->getCloudSearchEventAction()
                    ?: IndexingEventTriggerInterface::INDEXING_EVENT_DELETED_ACTION;

                foreach ($ids as $id) {
                    $this->addEvent($id, $instance->getCloudSearchEntityType(), $action);
                }
            }
        }

        IndexingEventProfiler::getInstance()->addToTotalTime(microtime(true) - $startTime);
    }

    public function postFlush(PostFlushEventArgs $args)
    {
        $startTime = microtime(true);

        if (!empty(static::$events)) {
            $apiClient = new ServiceApiClient();

            $sendStartTime = microtime(true);

            $apiClient->sendWebhookEvent(array_values(static::$events));

            IndexingEventProfiler::getInstance()->addToSendTime(microtime(true) - $sendStartTime);

            static::$events = [];
        }

        if (!empty(static::$importEvents)) {
            $this->updateCsLastUpdate();

            static::$importEvents = [];
        }

        IndexingEventProfiler::getInstance()->addToTotalTime(microtime(true) - $startTime);
    }

    protected function updateCsLastUpdate()
    {
        $categoryIds = [];
        $productIds  = [];

        foreach (static::$importEvents as $e) {
            list($entityType, $action) = explode('.', $e['eventType']);

            if ($entityType === IndexingEventTriggerInterface::INDEXING_EVENT_PRODUCT_ENTITY
                && in_array($action, [
                    IndexingEventTriggerInterface::INDEXING_EVENT_CREATED_ACTION,
                    IndexingEventTriggerInterface::INDEXING_EVENT_UPDATED_ACTION,
                ])
            ) {
                $productIds[] = $e['entityId'];
            }

            if ($entityType === IndexingEventTriggerInterface::INDEXING_EVENT_CATEGORY_ENTITY
                && in_array($action, [
                    IndexingEventTriggerInterface::INDEXING_EVENT_CREATED_ACTION,
                    IndexingEventTriggerInterface::INDEXING_EVENT_UPDATED_ACTION,
                ])
            ) {
                $categoryIds[] = $e['entityId'];
            }
        }

        if ($productIds) {
            Database::getEM()
                ->createQuery(
                    'UPDATE XLite\Model\Product p SET p.csLastUpdate = :timestamp WHERE p.product_id IN (:ids)'
                )
                ->setParameter('timestamp', LC_START_TIME)
                ->setParameter('ids', $productIds)
                ->execute();
        }

        if ($categoryIds) {
            Database::getEM()
                ->createQuery(
                    'UPDATE XLite\Model\Category c SET c.csLastUpdate = :timestamp WHERE c.category_id IN (:ids)'
                )
                ->setParameter('timestamp', LC_START_TIME)
                ->setParameter('ids', $categoryIds)
                ->execute();
        }
    }

    protected function addEvent($id, $type, $action)
    {
        $key = $type . '_' . $id;

        if (array_key_exists($key, static::$events)
            && $action !== IndexingEventTriggerInterface::INDEXING_EVENT_DELETED_ACTION
        ) {
            return;
        }

        if (XLite::getController() instanceof XLite\Controller\Admin\Import) {
            static::$importEvents[$key] = [
                'entityId'  => $id,
                'eventType' => $type . '.' . $action,
            ];
        } else {
            static::$events[$key] = [
                'entityId'  => $id,
                'eventType' => $type . '.' . $action,
            ];
        }
    }

    static public function triggerLatestChangesReindex()
    {
        $startTime = microtime(true);

        $tmpVar = Database::getRepo('XLite\Model\TmpVar');

        $updatedSince = $tmpVar->getVar('csImportStarted');

        if (!$updatedSince) {
            return;
        }

        $tmpVar->removeVar('csImportStarted');

        $position = 0;

        do {
            $productEvents = Database::getEM()
                ->createQuery(
                    "SELECT p.product_id as entityId, 'product.updated' as eventType 
                         FROM XLite\Model\Product p 
                         WHERE p.csLastUpdate >= :timestamp")
                ->setParameter('timestamp', $updatedSince)
                ->setFirstResult($position)
                ->setMaxResults(IndexingEventCore::MAX_RESULTS)
                ->getResult();

            if ($productEvents) {
                $apiClient = new ServiceApiClient();

                $sendStartTime = microtime(true);

                $apiClient->sendWebhookEvent($productEvents);

                IndexingEventProfiler::getInstance()->addToSendTime(microtime(true) - $sendStartTime);
            }

            $position += IndexingEventCore::MAX_RESULTS;
        } while (count($productEvents) === IndexingEventCore::MAX_RESULTS);

        $position = 0;

        do {
            $categoryEvents = Database::getEM()
                ->createQuery(
                    "SELECT c.category_id as entityId, 'category.updated' as eventType 
                         FROM XLite\Model\Category c
                         WHERE c.csLastUpdate >= :timestamp")
                ->setParameter('timestamp', $updatedSince)
                ->setFirstResult($position)
                ->setMaxResults(IndexingEventCore::MAX_RESULTS)
                ->getResult();

            if ($categoryEvents) {
                $apiClient = new ServiceApiClient();

                $sendStartTime = microtime(true);

                $apiClient->sendWebhookEvent($categoryEvents);

                IndexingEventProfiler::getInstance()->addToSendTime(microtime(true) - $sendStartTime);
            }

            $position += IndexingEventCore::MAX_RESULTS;
        } while (count($categoryEvents) === IndexingEventCore::MAX_RESULTS);

        IndexingEventProfiler::getInstance()->addToTotalTime(microtime(true) - $startTime);
    }
}