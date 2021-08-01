<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\RESTAPI\Model\Repo;

/**
 * Session repository
 */
abstract class Session extends \XLite\Model\Repo\SessionAbstract implements \XLite\Base\IDecorator
{

    /**
     * Find one entity for REST API
     *
     * @param mixed $id Entity ID
     *
     * @return \XLite\Model\AEntity
     */
    public function findOneForREST($id)
    {
        return $this->isPublicSessionIdValid($id)
            ? $this->findOneBySid($id)
            : parent::findOneForREST($id);
    }

}
