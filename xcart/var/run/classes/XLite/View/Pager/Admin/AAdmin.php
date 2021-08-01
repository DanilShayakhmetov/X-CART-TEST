<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Pager\Admin;

/**
 * AAdmin
 */
abstract class AAdmin extends \XLite\View\Pager\APager
{
    /**
     * getItemsPerPageDefault
     *
     * @return integer
     */
    protected function getItemsPerPageDefault()
    {
        return 30;
    }

    /**
     * Return number of pages to display
     *
     * @return integer
     */
    protected function getPagesPerFrame()
    {
        return 4;
    }

    /**
     * Return an array with information on the pages to be displayed
     *
     * @return array
     */
   protected function getPages()
    {
        if ($this->getPagesCount() < 4) {
            if (!isset($this->pages)) {
                $this->pages = [];
                $this->buildPlainPageList();
                $this->preparePagesForView();
            }

            return $this->pages;
        } else {
            return parent::getPages();
        }
    }

    /**
     * Add some additional information for the pages inner structure which is specific for view
     *
     * @return void
     */
    protected function preparePagesForView()
    {
        foreach ($this->pages as $k => $page) {

            $num = $page['num'] ?? null;
            $type = $page['type'];

            $isItem = isset($num) && (in_array($type, ['item', 'first-page', 'last-page'], true));

            $isOmittedItems = 'more-pages' === $type;
            $isSpecialItem = !$isItem && !$isOmittedItems;

            $isCurrent = isset($num) && $this->isCurrentPage($num);
            $isSelected = $isItem && $isCurrent;
            $isDisabled = $isSpecialItem && $isCurrent;

            $isActive = (!$isSelected && !$isOmittedItems && !$isDisabled && !\XLite::isAdminZone())
                || ($isSelected && \XLite::isAdminZone());

            if ($isItem || 'first-page' === $type || 'last-page' === $type) {
                $this->pages[$k]['text'] = $num;
            } elseif ($isOmittedItems) {
                $this->pages[$k]['text'] = '...';
            } elseif ('previous-page' === $type && \XLite::isAdminZone()) {
                $this->pages[$k]['text'] = '<i class="fa fa-angle-left"></i>';
            } elseif ('next-page' === $type && \XLite::isAdminZone()) {
                $this->pages[$k]['text'] = '<i class="fa fa-angle-right"></i>';
            } else {
                $this->pages[$k]['text'] = '&nbsp;';
            }

            $this->pages[$k]['page'] = !isset($num) ? null : 'page-' . $num;

            $needHrefForCustomer = isset($num)
                && !$isSelected
                && !$isDisabled
                && !\XLite::isAdminZone();

            $needHrefForAdmin = isset($num)
                && !$isOmittedItems
                && \XLite::isAdminZone();

            if ($needHrefForCustomer || $needHrefForAdmin) {
                $this->pages[$k]['href'] = $this->buildURLByPageId($num);
            }

            $classes = array(
                'item'                   => $isItem || $isSpecialItem,
                'selected'               => $isSelected,
                'disabled'               => $isDisabled,
                'active'                 => $isActive,
                $this->pages[$k]['page'] => $isItem,
                $type                    => true,
            );

            $css = array();

            foreach ($classes as $class => $enabled) {
                if ($enabled) {
                    $css[] = $class;
                }
            }

            $this->pages[$k]['classes'] = implode(' ', $css);
        }
    }
}
