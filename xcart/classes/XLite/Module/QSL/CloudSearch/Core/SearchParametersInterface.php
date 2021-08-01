<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\CloudSearch\Core;


/**
 * SearchParameters interface
 * SearchParameters produce CloudSearch search request parameters from CommonCell conditions
 */
interface SearchParametersInterface
{
    /**
     * Get search parameters
     *
     * @return array
     */
    public function getParameters();
}
