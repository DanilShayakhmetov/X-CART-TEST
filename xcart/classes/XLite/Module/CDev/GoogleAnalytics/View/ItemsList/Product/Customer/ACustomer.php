<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\GoogleAnalytics\View\ItemsList\Product\Customer;


use XLite\View\Pager\APager;
use XLite\View\Product\ListItem;

/**
 * Class ACustomer
 */
abstract class ACustomer extends \XLite\View\ItemsList\Product\Customer\ACustomer implements \XLite\Base\IDecorator
{
    /**
     * @var int
     */
    protected $ga_position_in_list = 1;

    /**
     * Get a list of JavaScript files required to display the widget properly
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        if (\XLite\Module\CDev\GoogleAnalytics\Main::isECommerceEnabled()) {
            $list[] = 'modules/CDev/GoogleAnalytics/items_list/product/products_list.js';
        }

        return $list;
    }

    /**
     * Get product list item widget params required for the widget of type getProductWidgetClass().
     *
     * @param \XLite\Model\Product $product
     *
     * @return array
     */
    protected function getProductWidgetParams(\XLite\Model\Product $product)
    {
        $result = parent::getProductWidgetParams($product);

        \XLite\Core\Translation::setTmpTranslationCode(\XLite\Core\Config::getInstance()->General->default_language);

        $result[ListItem::PARAM_LIST_READABLE_NAME]     = $this->getHead();
        $result[ListItem::PARAM_GA_POSITION_ON_LIST]    = $this->getProductPositionInListForGA();

        \XLite\Core\Translation::setTmpTranslationCode(null);
        return $result;
    }

    /**
     * N.B. changes internal position counter
     *
     * @return integer
     */
    protected function getProductPositionInListForGA()
    {
        $pageId = intval(\XLite\Core\Request::getInstance()->{APager::PARAM_PAGE_ID}) ?: 1;
        $itemsPerPage = intval($this->getPager()->getItemsPerPage());

        return ($pageId - 1) * $itemsPerPage + $this->ga_position_in_list++;
    }
}
