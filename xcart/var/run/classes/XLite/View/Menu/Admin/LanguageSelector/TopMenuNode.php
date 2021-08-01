<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Menu\Admin\LanguageSelector;

/**
 * Language selector top menu node
 *
 * @ListChild (list="admin.main.page.header.right", weight="200", zone="admin")
 */
class TopMenuNode extends \XLite\View\AView
{
    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'menu/language_selector/top_menu_node.twig';
    }

    /**
     * @return boolean
     */
    protected function isVisible()
    {
        if (parent::isVisible() && \XLite\Core\Auth::getInstance()->isAdmin()) {
            $activeLanguages = \XLite\Core\Database::getRepo('XLite\Model\Language')->findActiveLanguages();

            return count($activeLanguages) > 1;
        }

        return false;
    }
}
