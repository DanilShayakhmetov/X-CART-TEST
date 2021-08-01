<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\CloudSearch\View\ItemsList\Product\Customer;

use XLite\Core\CommonCell;
use XLite\Core\Request;
use XLite\Module\QSL\CloudSearch\Model\Repo\Product as ProductRepo;


/**
 * Decorated ACustomer items list
 *
 * @Decorator\Depend({"XC\NextPreviousProduct"})
 */
abstract class ACustomerNextPrevious extends \XLite\View\ItemsList\Product\Customer\ACustomer implements \XLite\Base\IDecorator
{
    const PARAM_CLOUD_FILTERS = 'cloudFilters';

    const PARAM_LOAD_PRODUCTS_WITH_CLOUD_SEARCH = 'loadProductsWithCloudSearch';

    const PARAM_VENDOR_ID = 'vendor_id';

    const PARAM_BRAND_ID = 'brand_id';

    /**
     * @return CommonCell
     */
    protected function getNextPreviousSearchCondition()
    {
        $cnd = parent::getNextPreviousSearchCondition();

        $cnd->{ProductRepo::P_CLOUD_FILTERS} = $this->getSavedRequestParam(self::PARAM_CLOUD_FILTERS);
        if (!empty($cnd->{ProductRepo::P_CLOUD_FILTERS})) {
            $cnd->{ProductRepo::P_LOAD_PRODUCTS_WITH_CLOUD_SEARCH} = true;
        }

        $sessionCell = $this->getSessionCell();
        $page = '';

        if (strpos($sessionCell, 'XLiteViewItemsListProductCustomerCategoryMain') !== false) {
            $page = 'category';
        } elseif (strpos($sessionCell, 'XLiteModuleXCMultiVendorViewItemsListProductCustomerVendor') !== false) {
            $page = 'vendor';
        } elseif (strpos($sessionCell, 'XLiteModuleQSLShopByBrandViewItemsListProductCustomerBrand') !== false) {
            $page = 'brand';
        }

        if (in_array($page, ['category', 'vendor', 'brand'])
            && $this->getSavedRequestParam(self::PARAM_SORT_BY)
            && $this->getSavedRequestParam(self::PARAM_SORT_ORDER)
        ) {
            $cnd->{ProductRepo::P_ORDER_BY} = [
                $this->getSavedRequestParam(self::PARAM_SORT_BY),
                $this->getSavedRequestParam(self::PARAM_SORT_ORDER),
            ];
        }

        if ($page === 'vendor' && $this->getSavedRequestParam(self::PARAM_VENDOR_ID)) {
            $cnd->{ProductRepo::P_VENDOR_ID} = $this->getSavedRequestParam(self::PARAM_VENDOR_ID);
        }

        if ($page === 'brand' && $this->getSavedRequestParam(self::PARAM_BRAND_ID)) {
            Request::getInstance()->__set(self::PARAM_BRAND_ID, $this->getSavedRequestParam(self::PARAM_BRAND_ID));
        }

        return $cnd;
    }
}
