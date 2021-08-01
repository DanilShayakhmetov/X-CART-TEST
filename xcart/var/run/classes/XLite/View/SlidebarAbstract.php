<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * Welcome page
 *
 *  ListChild (list="layout.slidebar", zone="customer", weight="10")
 */
abstract class SlidebarAbstract extends \XLite\View\AView implements \XLite\Core\PreloadedLabels\ProviderInterface
{
    /**
     * Get a list of JavaScript files required to display the widget properly
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'js/jquery.mmenu/jquery.mmenu.min.all.js';
        $list[] = 'js/slidebar.js';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'layout/slidebar.twig';
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible() && !$this->isCheckoutLayout();
    }

    /**
     * Checks if widget content should be rendered
     *
     * @return boolean
     */
    protected function shouldRender()
    {
        return \XLite::getController()->shouldRenderMobileNavbar();
    }

    /**
     * Array of labels in following format.
     *
     * 'label' => 'translation'
     *
     * @return mixed
     */
    public function getPreloadedLanguageLabels()
    {
        $list = array(
            'Menu',
        );

        $data = array();
        foreach ($list as $name) {
            $data[$name] = static::t($name);
        }

        return $data;
    }

    /**
     * @return bool
     */
    protected function isDisplayCategories()
    {
        $root = \XLite\Core\Database::getRepo('XLite\Model\Category')->getRootCategory();
        return $root ? $root->hasSubcategories() : false;
    }
}
