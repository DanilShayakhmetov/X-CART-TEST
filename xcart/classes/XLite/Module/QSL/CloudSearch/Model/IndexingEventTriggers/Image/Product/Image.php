<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\CloudSearch\Model\IndexingEventTriggers\Image\Product;

use XLite\Module\QSL\CloudSearch\Core\IndexingEvent\IndexingEventTriggerInterface;


/**
 * Product image
 */
class Image extends \XLite\Model\Image\Product\Image  implements \XLite\Base\IDecorator, IndexingEventTriggerInterface
{
    public function getCloudSearchEntityType()
    {
        return self::INDEXING_EVENT_PRODUCT_ENTITY;
    }

    public function getCloudSearchEntityIds()
    {
        return [$this->getProduct()->getProductId()];
    }

    public function getCloudSearchEventAction()
    {
        return self::INDEXING_EVENT_UPDATED_ACTION;
    }
}
