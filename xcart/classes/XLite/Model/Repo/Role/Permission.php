<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Repo\Role;

/**
 * Permission repository
 *
 * @Api\Operation\Read(modelClass="XLite\Model\Role\Permission", summary="Retrieve user permission type by id")
 * @Api\Operation\ReadAll(modelClass="XLite\Model\Role\Permission", summary="Retrieve all user permission types")
 */
class Permission extends \XLite\Model\Repo\Base\I18n
{

    /**
     * Alternative record identifiers
     *
     * @var array
     */
    protected $alternativeIdentifier = array(
        array('code'),
    );

}
