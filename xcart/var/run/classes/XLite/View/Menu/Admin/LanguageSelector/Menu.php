<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Menu\Admin\LanguageSelector;

/**
 * Quick menu widget
 */
class Menu extends \XLite\View\Menu\Admin\AAdmin
{
    /**
     * Return widget directory
     *
     * @return string
     */
    protected function getDir()
    {
        return 'menu/language_selector';
    }

    /**
     * Get default widget
     *
     * @return string
     */
    protected function getDefaultWidget()
    {
        return 'XLite\View\Menu\Admin\LanguageSelector\Node';
    }

    /**
     * Define menu items
     *
     * @return array
     */
    protected function defineItems()
    {
        $items = [];

        $activeLanguages = $this->getActiveLanguages();

        if (count($activeLanguages) > 1) {
            $weight = 1;
            foreach ($activeLanguages as $lng) {
                $items[$lng->getCode()] = [
                    static::ITEM_TITLE         => strtoupper($lng->getCode()),
                    static::ITEM_LINK          => $this->getChangeLanguageLink($lng),
                    static::ITEM_WEIGHT        => $weight++,
                    static::ITEM_ICON_IMG      => \XLite\Core\Layout::getInstance()->getResourceWebPath(
                        $lng->getFlagFile(),
                        null,
                        \XLite\Core\Layout::PATH_COMMON
                    ),
                    static::ITEM_PUBLIC_ACCESS => true,
                ];
            }
        }

        return $items;
    }

    /**
     * Get active languages
     *
     * @return \XLite\Model\Language[]
     */
    protected function getActiveLanguages()
    {
        $list = [];
        foreach (\XLite\Core\Database::getRepo('XLite\Model\Language')->findActiveLanguages() as $language) {
            $list[] = $language;
        }

        return $list;
    }

    /**
     * Get link to change language
     *
     * @param \XLite\Model\Language $language Language object
     *
     * @return string
     */
    protected function getChangeLanguageLink(\XLite\Model\Language $language)
    {
        return $this->buildURL(
            $this->getTarget(),
            'change_language',
            array(
                'language' => $language->getCode(),
            ) + $this->getAllParams(),
            false
        );
    }
}
