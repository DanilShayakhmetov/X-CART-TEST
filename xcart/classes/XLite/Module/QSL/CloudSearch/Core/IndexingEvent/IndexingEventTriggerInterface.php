<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\CloudSearch\Core\IndexingEvent;

interface IndexingEventTriggerInterface
{
    const INDEXING_EVENT_CATEGORY_ENTITY = 'category';
    const INDEXING_EVENT_PRODUCT_ENTITY = 'product';

    const INDEXING_EVENT_CREATED_ACTION = 'created';
    const INDEXING_EVENT_UPDATED_ACTION = 'updated';
    const INDEXING_EVENT_DELETED_ACTION = 'deleted';

    public function getCloudSearchEntityType();
    public function getCloudSearchEntityIds();
    public function getCloudSearchEventAction();
}