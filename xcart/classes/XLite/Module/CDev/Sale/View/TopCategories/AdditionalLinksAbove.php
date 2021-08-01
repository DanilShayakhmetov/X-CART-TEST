<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Sale\View\TopCategories;

use XLite\Module\CDev\Sale\View\FormField\Select\ShowLinksInCategoryMenu;

/**
 * List of discount links
 *
 * @ListChild (list="topCategories.linksAbove", zone="customer", weight="100")
 */
class AdditionalLinksAbove extends \XLite\Module\CDev\Sale\View\TopCategories\AAdditionalLinks
{
    protected function isVisible()
    {
        return parent::isVisible()
            && ShowLinksInCategoryMenu::TYPE_ABOVE_CATEGORIES === \XLite\Core\Config::getInstance()->CDev->Sale->show_links_in_category_menu;
    }
}
