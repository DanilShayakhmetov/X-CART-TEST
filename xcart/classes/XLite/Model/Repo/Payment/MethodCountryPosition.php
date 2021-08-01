<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Repo\Payment;

/**
 * Payment method country position
 */
class MethodCountryPosition extends \XLite\Model\Repo\ARepo
{
    /**
     * Find one by record
     *
     * @param array                $data   Record
     * @param \XLite\Model\AEntity $parent Parent model OPTIONAL
     *
     * @return \XLite\Model\AEntity|void
     */
    public function findOneByRecord(array $data, \XLite\Model\AEntity $parent = null)
    {
        if (empty($data['countryCode'])) {
            $data['countryCode'] = \XLite\Core\Config::getInstance()->Company->location_country;
        }

        return isset($parent) ? $parent->getCountryPosition($data['countryCode']) : parent::findOneByRecord($data, $parent);
    }
}
