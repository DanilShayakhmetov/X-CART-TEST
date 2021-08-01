<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\ProductAdvisor\View;

use XLite\Core\Database;

/**
 * New arrivals block widget
 *
 *  ListChild (list="sidebar.second", zone="customer", weight="600", name="main")
 *  ListChild (list="center.bottom", zone="customer", weight="480", preset="one", name="main")
 */
abstract class NewArrivalsAbstract extends \XLite\Module\CDev\ProductAdvisor\View\ANewArrivals
{
    /**
     * Flag: count all existing new arrival products or only displayed in widget
     *
     * @var boolean
     */
    protected $countAllNewProducts = false;

    /**
     * Return list of targets allowed for this widget
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $result   = parent::getAllowedTargets();
        $result[] = 'main';
        $result[] = 'category';

        return $result;
    }

    /**
     * Return search parameters.
     *
     * @return array
     */
    public static function getSearchParams()
    {
        return [
            \XLite\Model\Repo\Product::P_CATEGORY_ID => static::PARAM_CATEGORY_ID,
        ];
    }

    /**
     * Initialize widget (set attributes)
     *
     * @param array $params Widget params
     *
     * @return void
     */
    public function setWidgetParams(array $params)
    {
        parent::setWidgetParams($params);

        if (isset($params['viewListName']) && 'center.bottom' === $params['viewListName']) {
            $this->widgetParams[self::PARAM_WIDGET_TYPE]->setValue(self::WIDGET_TYPE_CENTER);
        }

        unset(
            $this->widgetParams[\XLite\View\Pager\APager::PARAM_SHOW_ITEMS_PER_PAGE_SELECTOR],
            $this->widgetParams[\XLite\View\Pager\APager::PARAM_ITEMS_PER_PAGE]
        );
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            self::PARAM_USE_NODE             => new \XLite\Model\WidgetParam\TypeCheckbox(
                'Show products only for current category',
                \XLite\Core\Config::getInstance()->CDev->ProductAdvisor->na_from_current_category,
                true
            ),
            self::PARAM_ROOT_ID              => new \XLite\Model\WidgetParam\ObjectId\Category(
                'Root category Id',
                $this->getMainRootId(),
                true,
                true
            ),
            self::PARAM_MAX_ITEMS_TO_DISPLAY => new \XLite\Model\WidgetParam\TypeInt(
                'Maximum products to display',
                $this->getMaxCountInBlock(),
                true,
                true
            ),
        ];

        $this->widgetParams[self::PARAM_WIDGET_TYPE]->setValue(self::WIDGET_TYPE_SIDEBAR);
        $this->widgetParams[self::PARAM_DISPLAY_MODE]->setValue(self::DISPLAY_MODE_GRID);

        unset(
            $this->widgetParams[self::PARAM_SHOW_DISPLAY_MODE_SELECTOR],
            $this->widgetParams[self::PARAM_SHOW_SORT_BY_SELECTOR]
        );
    }

    /**
     * Default search conditions
     *
     * @param  \XLite\Core\CommonCell $searchCase Search case
     *
     * @return \XLite\Core\CommonCell
     */
    protected function postprocessSearchCase(\XLite\Core\CommonCell $searchCase)
    {
        $searchCase = parent::postprocessSearchCase($searchCase);

        if ($this->getMaxItemsCount()) {
            $searchCase->{\XLite\Model\Repo\ARepo::P_LIMIT} = [ 0, $this->getMaxItemsCount() ];
        }

        $categoryId = $this->getRootId();

        if ($this->countAllNewProducts || !$categoryId) {
            unset(
                $searchCase->{\XLite\Model\Repo\Product::P_CATEGORY_ID},
                $searchCase->{\XLite\Model\Repo\Product::P_SEARCH_IN_SUBCATS}
            );

        } elseif ($categoryId) {
            $searchCase->{\XLite\Model\Repo\Product::P_CATEGORY_ID}       = $categoryId;
            $searchCase->{\XLite\Model\Repo\Product::P_SEARCH_IN_SUBCATS} = true;
        }

        return $searchCase;
    }

    /**
     * @return \XLite\Core\CommonCell
     */
    protected function getLimitCondition()
    {
        $cnd = $this->getSearchCondition();

        if ($this->countAllNewProducts) {
            return $this->getPager()->getLimitCondition(0, $this->getMaxItemsCount(), $cnd);
        }

        return $cnd;
    }

    /**
     * getSidebarMaxItems
     *
     * @return integer
     */
    protected function getSidebarMaxItems()
    {
        return $this->getMaxItemsCount();
    }

    /**
     * Returns maximum allowed items count
     *
     * @return integer
     */
    protected function getMaxItemsCount()
    {
        return $this->getParam(self::PARAM_MAX_ITEMS_TO_DISPLAY) ?: $this->getMaxCountInBlock();
    }

    /**
     * Return main category Id to use
     *
     * @return integer
     */
    protected function getMainRootId()
    {
        return Database::getRepo('XLite\Model\Category')->getRootCategoryId();
    }

    /**
     * Return category Id to use
     *
     * @return integer
     */
    protected function getRootId()
    {
        return $this->getParam(self::PARAM_USE_NODE)
            ? (int) \XLite\Core\Request::getInstance()->category_id
            : $this->getParam(self::PARAM_ROOT_ID);
    }

    /**
     * Return template of New arrivals widget. It depends on widget type:
     * SIDEBAR/CENTER and so on.
     *
     * @return string
     */
    protected function getTemplate()
    {
        $template = parent::getTemplate();
        if ($template === $this->getDefaultTemplate()
            && self::WIDGET_TYPE_SIDEBAR === $this->getParam(self::PARAM_WIDGET_TYPE)
        ) {
            $template = self::TEMPLATE_SIDEBAR;
        }

        return $template;
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return static::getWidgetTarget() !== \XLite\Core\Request::getInstance()->target
        && parent::isVisible();
    }

    /**
     * Check status of 'More...' link for New arrivals list
     *
     * @return boolean
     */
    protected function isShowMoreLink()
    {
        $this->countAllNewProducts = true;

        $result = $this->getItemsCount() > $this->getMaxItemsCount();

        $this->countAllNewProducts = false;

        return $result;
    }

    /**
     * Get 'More...' link URL for New arrivals list
     *
     * @return string
     */
    protected function getMoreLinkURL()
    {
        return $this->buildURL(self::WIDGET_TARGET_NEW_ARRIVALS);
    }

    /**
     * Get 'More...' link text for New arrivals list
     *
     * @return string
     */
    protected function getMoreLinkText()
    {
        return \XLite\Core\Translation::getInstance()->translate('All newest products');
    }

    /**
     * Get cache parameters
     *
     * @return array
     */
    protected function getCacheParameters()
    {
        $list = parent::getCacheParameters();
        $list[] = $this->getRootId();

        return $list;
    }
}
