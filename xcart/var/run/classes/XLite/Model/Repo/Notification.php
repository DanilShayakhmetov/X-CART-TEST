<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Repo;

/**
 * Notifications repository
 *
 * @Api\Operation\Read(modelClass="XLite\Model\Notification", summary="Retrieve email notification type by id")
 * @Api\Operation\ReadAll(modelClass="XLite\Model\Notification", summary="Retrieve all email notification types")
 * @Api\Operation\Update(modelClass="XLite\Model\Notification", summary="Update email notification type by id")
 * @Api\Operation\Delete(modelClass="XLite\Model\Notification", summary="Delete email notification type by id")
 */
class Notification extends \XLite\Model\Repo\Base\I18n
{
    /**
     * Alternative record identifiers
     *
     * @var array
     */
    protected $alternativeIdentifier = array(
        array('templatesDirectory'),
    );

    protected $defaultOrderBy = 'position';
}
