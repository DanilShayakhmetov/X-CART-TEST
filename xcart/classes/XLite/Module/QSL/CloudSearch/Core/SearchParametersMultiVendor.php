<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\CloudSearch\Core;

use XLite\Core\Database;
use XLite\Module\QSL\CloudSearch\Model\Repo\Product;


/**
 * Produces CloudSearch search parameters from CommonCell conditions
 *
 * @Decorator\Depend ({"XC\MultiVendor"})
 */
class SearchParametersMultiVendor extends \XLite\Module\QSL\CloudSearch\Core\SearchParameters implements \XLite\Base\IDecorator
{
    /**
     * Get search parameters
     *
     * @return array
     */
    public function getParameters()
    {
        $data = parent::getParameters();

        if ($this->cnd->{Product::P_VENDOR_ID}) {
            $data['conditions']['vendor'] = [$this->cnd->{Product::P_VENDOR_ID}];
        }

        if ($this->cnd->{Product::P_VENDOR}) {
            $vendor = Database::getRepo('XLite\Model\Profile')
                ->findVendorsByTerm($this->cnd->{Product::P_VENDOR}, 1);

            if ($vendor) {
                $data['conditions']['vendor'] = [$vendor[0]->getProfileId()];
            }
        }

        return $data;
    }
}
