<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\RESTAPI\Model\Repo;

/**
 * Category repository
 */
 class Category extends \XLite\Model\Repo\CategoryAbstract implements \XLite\Base\IDecorator
{
    /**
     * Postprocess REST POST method
     *
     * @return void
     */
    public function postprocessPostRESTRequest()
    {
        $this->correctCategoriesStructure();
    }
}
