<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Coupons\View\FormModel\BulkEdit\Product;

/**
 * @Decorator\Depend ("XC\BulkEditing")
 */
class Coupons extends \XLite\Module\XC\BulkEditing\View\FormModel\Product\AProduct
{
    /**
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/CDev/Coupons/form_model/bulk_edit/product.less';

        return $list;
    }

    public function __construct(array $params)
    {
        $this->scenario = 'coupons';

        parent::__construct($params);
    }
}
