<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Reviews\View\Button\ItemsExport;

use XLite\View\Button\ExportCSV;
use XLite\View\Button\ItemsExport;
use XLite\Module\XC\Reviews\Logic\Export\Step\Reviews;
use XLite\Module\XC\Reviews\View\ItemsList\Model\ProductReview;

/**
 * Product ItemsExport button
 */
class ProductReviews extends ItemsExport
{
    protected function getAdditionalButtons()
    {
        $list = [];

        $list['CSV'] = $this->getWidget([
            'label'      => 'CSV',
            'style'      => 'always-enabled action link list-action',
            'icon-style' => '',
            'entity'     => Reviews::class,
            'session'    => ProductReview::getConditionCellName(),
        ], ExportCSV::class);

        return $list;
    }
}