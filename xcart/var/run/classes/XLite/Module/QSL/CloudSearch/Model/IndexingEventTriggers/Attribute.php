<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\CloudSearch\Model\IndexingEventTriggers;

use XLite\Module\QSL\CloudSearch\Core\IndexingEvent\IndexingEventCore;
use XLite\Module\QSL\CloudSearch\Core\IndexingEvent\IndexingEventTriggerInterface;


/**
 * Attribute model
 */
 class Attribute extends \XLite\Module\XC\GoogleFeed\Model\Attribute implements \XLite\Base\IDecorator, IndexingEventTriggerInterface
{
    public function getCloudSearchEntityType()
    {
        return self::INDEXING_EVENT_PRODUCT_ENTITY;
    }

    public function getCloudSearchEntityIds()
    {
        if (!$this->getProduct()) {
            return IndexingEventCore::findProductIdsByAttribute($this);
        } else {
            return [$this->getProduct()->getProductId()];
        }
    }

    public function getCloudSearchEventAction()
    {
        return self::INDEXING_EVENT_UPDATED_ACTION;
    }
}
