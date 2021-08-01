<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\CloudSearch\Model\IndexingEventTriggers;

use XLite\Module\QSL\CloudSearch\Core\IndexingEvent\IndexingEventTriggerInterface;


/**
 * Product model
 *
 * @Table (indexes={
 *      @Index (name="csLastUpdate", columns={"csLastUpdate"})
 *  }
 * )
 *
 * @MappedSuperclass
 */
 class Product extends \XLite\Module\QSL\CloudSearch\Model\Product implements \XLite\Base\IDecorator, IndexingEventTriggerInterface
{
    /**
     * Last update timestamp
     *
     * @var integer
     *
     * @Column (type="integer")
     */
    protected $csLastUpdate = 0;

    public function getCloudSearchEntityType()
    {
        return self::INDEXING_EVENT_PRODUCT_ENTITY;
    }

    public function getCloudSearchEntityIds()
    {
        return [$this->getId()];
    }

    public function getCloudSearchEventAction()
    {
        return null;
    }

    /**
     * @return int
     */
    public function getCsLastUpdate()
    {
        return $this->csLastUpdate;
    }

    /**
     * @param int $csLastUpdate
     */
    public function setCsLastUpdate($csLastUpdate)
    {
        $this->csLastUpdate = $csLastUpdate;
    }
}
