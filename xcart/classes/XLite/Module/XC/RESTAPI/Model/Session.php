<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\RESTAPI\Model;

/**
 * Session
 */
abstract class Session extends \XLite\Model\Session implements \XLite\Base\IDecorator
{
    /**
     * Build associations fields
     *
     * @return array
     */
    protected function buildAssociationsforREST()
    {
        $data = parent::buildAssociationsforREST();

        $data['cells'] = array();
        foreach ((array) static::getSessionCellRepo()->findById($this->getId()) as $cell) {
            $data['cells'][] = $cell->buildDataForREST(false);
        }

        return $data;
    }
}
