<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ItemsList\Product;

/**
 * Abstract product list
 */
abstract class AProductAbstract extends \XLite\View\ItemsList\AItemsList
{
    /**
     * Allowed sort criteria
     */
    const SORT_BY_MODE_PRICE  = 'p.price';
    const SORT_BY_MODE_NAME   = 'translations.name';
    const SORT_BY_MODE_SKU    = 'p.sku';
    const SORT_BY_MODE_AMOUNT = 'p.amount';

    /**
     * Return current display mode
     *
     * @return string
     */
    abstract protected function getDisplayMode();


    /**
     * Define and set widget attributes; initialize widget
     *
     * @param array $params Widget params OPTIONAL
     */
    public function __construct(array $params = array())
    {
        $this->sortByModes += array(
            self::SORT_BY_MODE_PRICE  => 'Price',
            self::SORT_BY_MODE_NAME   => 'Name',
            self::SORT_BY_MODE_SKU    => 'SKU',
            self::SORT_BY_MODE_AMOUNT => 'Amount',
        );

        parent::__construct($params);
    }

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = $this->getDir() . '/products_list.css';

        return $list;
    }

    /**
     * Get a list of JavaScript files required to display the widget properly
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = $this->getDir() . '/products_list.js';

        return $list;
    }

    /**
     * Returns a list of CSS classes (separated with a space character) to be attached to the items list
     *
     * @return string
     */
    public function getListCSSClasses()
    {
        return parent::getListCSSClasses() . ' items-list-products';
    }

    /**
     * Return name of the base widgets list
     *
     * @return string
     */
    protected function getListName()
    {
        return parent::getListName() . '.product'
            . (null === $this->getDisplayMode() ?: '.' . $this->getDisplayMode());
    }

    /**
     * Get widget templates directory
     * NOTE: do not use "$this" pointer here (see "get[CSS/JS]Files()")
     *
     * @return string
     */
    protected function getDir()
    {
        return parent::getDir() . '/product';
    }

    /**
     * Return dir which contains the page body template
     *
     * @return string
     */
    protected function getPageBodyDir()
    {
        return str_replace('.', '/', $this->getDisplayMode());
    }

    /**
     * getSortByModeDefault
     *
     * @return string
     */
    protected function getSortByModeDefault()
    {
        return self::SORT_BY_MODE_NAME;
    }

    /**
     * Define repository name
     *
     * @return string
     */
    protected function defineRepositoryName()
    {
        return 'XLite\Model\Product';
    }

    /**
     * getJSHandlerClassName
     *
     * @return string
     */
    protected function getJSHandlerClassName()
    {
        return 'ProductsList';
    }
}
