<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Menu\Admin;

use XLite\Core\Cache\ExecuteCached;

/**
 * Abstract admin menu
 */
abstract class AAdminAbstract extends \XLite\View\Menu\AMenu
{
    /**
     * Item parameter names
     */
    const ITEM_TITLE         = 'title';
    const ITEM_TOOLTIP       = 'tooltip';
    const ITEM_LINK          = 'link';
    const ITEM_BLOCK         = 'block';
    const ITEM_LIST          = 'list';
    const ITEM_CLASS         = 'className';
    const ITEM_TARGET        = 'linkTarget';
    const ITEM_EXTRA         = 'extra';
    const ITEM_PERMISSION    = 'permission';
    const ITEM_PUBLIC_ACCESS = 'publicAccess';
    const ITEM_CHILDREN      = 'children';
    const ITEM_WEIGHT        = 'weight';
    const ITEM_WIDGET        = 'widget';
    const ITEM_BLANK_PAGE    = 'blankPage';
    const ITEM_ICON_FONT     = 'iconFont';
    const ITEM_ICON_SVG      = 'iconSVG';
    const ITEM_ICON_HTML     = 'iconHTML';
    const ITEM_ICON_IMG      = 'iconIMG';
    const ITEM_LABEL         = 'label';
    const ITEM_LABEL_LINK    = 'labelLink';
    const ITEM_LABEL_TITLE   = 'labelTitle';

    /**
     * Array of targets related to the same menu link
     *
     * @var array
     */
    protected $relatedTargets = [
        'orders_stats'         => [
            'top_sellers',
        ],
        'order_list'           => [
            'order',
        ],
        'payment_transactions' => [
            'payment_transaction',
        ],
        'product_list'         => [
            'product',
        ],
        'categories'           => [
            'category',
            'category_products',
        ],
        'front_page'           => [
            'banner_rotation',
        ],
        'profile_list'         => [
            'profile',
            'address_book',
        ],
        'shipping_methods'     => [
            'shipping_rates',
            'shipping_test',
            'origin_address',
            'automate_shipping_refunds',
            'automate_shipping_routine',
        ],
        'countries'            => [
            'zones',
            'states',
        ],
        'payment_settings'     => [
            'payment_method',
            'payment_appearance',
        ],
        'db_backup'            => [
            'db_restore',
        ],
        'integrity_check'      => [],
        'consistency_check'    => [],
        'product_classes'      => [
            'product_class',
            'attributes',
        ],
        'tax_classes'          => [
            'tax_class',
        ],
        'units_formats'        => [
            'currency',
        ],
        'languages'            => [
            'labels',
        ],
        'general_settings'     => [
            'shipping_settings',
            'address_fields',
        ],
        'notifications'        => [
            'notification',
            'notification_common',
            'notification_attachments',
            'email_settings',
            'test_email',
        ],
        'custom_css'           => [
            'custom_js',
        ],
    ];

    protected $relatedExtraTargets = [
        ['volume_discount', 'promotions', [], ['page' => 'volume_discounts']],
    ];

    /**
     * Selected item
     *
     * @var array
     */
    protected $selectedItem = [];

    /**
     * @var SelectedDecider
     */
    protected $selectedDecider;

    /**
     * @param SelectedDecider $selectedDecider
     */
    public function setSelectedDecider($selectedDecider)
    {
        $this->selectedDecider = $selectedDecider;
    }

    /**
     * @return SelectedDecider
     */
    public function getSelectedDecider()
    {
        return $this->selectedDecider;
    }

    /**
     * Return widget directory
     *
     * @return string
     */
    abstract protected function getDir();

    /**
     * Get default widget
     *
     * @return string
     */
    abstract protected function getDefaultWidget();

    /**
     * Add related target for selected menu item
     *
     * @param string $target
     * @param string $destTarget
     * @param array  $extra
     * @param array  $destExtra
     *
     * @return $this
     */
    public function addRelatedTarget(string $target, string $destTarget, array $extra = [], array $destExtra = [])
    {
        $this->relatedExtraTargets[] = [
            $target,
            $destTarget,
            $extra,
            $destExtra,
        ];

        return $this;
    }

    /**
     * Get target weight
     *
     * @param string $currentTarget
     * @param string $target
     * @param array  $extra
     *
     * @return mixed
     */
    public function getTargetWeight(string $currentTarget, string $target, array $extra = [])
    {
        $weights = [0];
        $request = \XLite\Core\Request::getInstance();

        // find in related targets
        $relatedTargets = $this->getRelatedTargets($target);

        if (in_array($currentTarget, $relatedTargets, true)) {
            $weight = 1;

            foreach ($extra as $key => $value) {
                if ($request->$key !== $value) {
                    $weight = 0;
                    continue;
                }

                $weight++;
            }

            $weights[] = $weight;
        }

        // find related targets with extra
        foreach ($this->relatedExtraTargets as [$relatedTarget, $originTarget, $relatedExtra, $originExtra]) {
            if (
                $currentTarget !== $relatedTarget
                || $target !== $originTarget
                || array_diff($extra, $originExtra)
            ) {
                continue;
            }

            $weights[] = 1;

            foreach ($relatedExtra as $key => $value) {
                if ($request->$key !== $value) {
                    continue 2;
                }
            }

            $weights[] = count($relatedExtra);
        }

        return max($weights);
    }

    /**
     * Returns the list of related targets
     *
     * @param string $target Target name
     *
     * @return array
     */
    public function getRelatedTargets($target)
    {
        return isset($this->relatedTargets[$target])
            ? array_merge([$target], $this->relatedTargets[$target])
            : [$target];
    }

    /**
     * Sort items
     *
     * @param array $item1 Item 1
     * @param array $item2 Item 2
     *
     * @return boolean
     */
    protected function sortItems($item1, $item2)
    {
        $weight1 = isset($item1[static::ITEM_WEIGHT]) ? intval($item1[static::ITEM_WEIGHT]) : 0;
        $weight2 = isset($item2[static::ITEM_WEIGHT]) ? intval($item2[static::ITEM_WEIGHT]) : 0;

        return $weight1 > $weight2;
    }

    /**
     * Mark selected
     *
     * @param array $items Items
     *
     * @return array
     */
    protected function markSelected($items)
    {
        if (!empty($this->selectedItem)
            && $items
        ) {
            foreach ($items as $index => $item) {
                if ($index == $this->selectedItem['index']) {
                    $item->setWidgetParams(
                        [
                            \XLite\View\Menu\Admin\LeftMenu\Node::PARAM_SELECTED => true,
                        ]
                    );
                    break;

                } elseif ($item->getParam(static::ITEM_CHILDREN)) {
                    $items[$index]->setWidgetParams(
                        [
                            static::ITEM_CHILDREN => $this->markSelected($item->getParam(static::ITEM_CHILDREN)),
                        ]
                    );

                    $result = false;
                    foreach ($item->getParam(static::ITEM_CHILDREN) as $child) {
                        if ($child->getParam(\XLite\View\Menu\Admin\LeftMenu\Node::PARAM_SELECTED)
                            || $child->getParam(\XLite\View\Menu\Admin\LeftMenu\Node::PARAM_EXPANDED)
                        ) {
                            $result = true;
                            break;
                        }

                    }

                    if ($result) {
                        $item->setWidgetParams(
                            [
                                \XLite\View\Menu\Admin\LeftMenu\Node::PARAM_EXPANDED => true,
                            ]
                        );
                    }
                }
            }
        }

        return $items;
    }

    /**
     * Get menu items
     *
     * @return array
     */
    protected function getItems()
    {
        if (!isset($this->items)) {
            $items = $this->defineItems();

            $this->setSelectedDecider(
                $this->createSelectedDecider('getItemsForDecider')
            );

            $this->items = $this->prepareItems($items);
        }

        return $this->items;
    }

    /**
     * Get menu items
     *
     * @return array
     */
    public function getItemsForDecider()
    {
        $cacheParams = [
            'getItemsForDecider',
            get_class($this),
        ];

        return ExecuteCached::executeCachedRuntime(function () {
            return $this->defineItems();
        }, $cacheParams);
    }

    /**
     * @param $getter
     *
     * @return SelectedDecider
     */
    protected function createSelectedDecider($getter)
    {
        return new SelectedDecider(get_class($this), $getter);
    }

    /**
     * Prepare items
     *
     * @param array $items Items
     *
     * @return array
     */
    protected function prepareItems($items)
    {
        $selectedDecider = $this->getSelectedDecider();

        uasort($items, [$this, 'sortItems']);
        foreach ($items as $index => $item) {
            if (isset($item[static::ITEM_CHILDREN])
                && is_array($item[static::ITEM_CHILDREN])
                && !empty($item[static::ITEM_CHILDREN])
            ) {
                $item[static::ITEM_CHILDREN]                            = $this->prepareItems($item[static::ITEM_CHILDREN]);
                $item[\XLite\View\Menu\Admin\LeftMenu\Node::PARAM_LIST] = $index;

            } elseif (isset($item[static::ITEM_CHILDREN])) {
                $item[static::ITEM_CHILDREN] = [];
            }

            $item[\XLite\View\Menu\Admin\LeftMenu\Node::PARAM_TITLE]   = empty($item[static::ITEM_TITLE])
                ? ''
                // : static::t($item[static::ITEM_TITLE]);
                : $item[static::ITEM_TITLE];
            $item[\XLite\View\Menu\Admin\LeftMenu\Node::PARAM_TOOLTIP] = empty($item[static::ITEM_TOOLTIP])
                ? ''
                // : static::t($item[static::ITEM_TOOLTIP]);
                : $item[static::ITEM_TOOLTIP];

            $item[\XLite\View\Menu\Admin\LeftMenu\Node::PARAM_SELECTED_DECIDER] = $selectedDecider;
            $item[\XLite\View\Menu\Admin\LeftMenu\Node::PARAM_NAME]             = $index;

            if (empty($item[\XLite\View\Menu\Admin\LeftMenu\Node::PARAM_CLASS]) && is_string($index)) {
                $item[\XLite\View\Menu\Admin\LeftMenu\Node::PARAM_CLASS] = str_replace('_', '-', $index);
            }

            $items[$index] = $this->getWidget(
                $item,
                isset($item[static::ITEM_WIDGET]) ? $item[static::ITEM_WIDGET] : $this->getDefaultWidget()
            );

            if (!$items[$index]->checkACL()
                || !$items[$index]->isVisible()
            ) {
                unset($items[$index]);
            }
        }

        return $items;
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return \XLite\Core\Auth::getInstance()->isAdmin()
            && parent::isVisible();
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . '/body.twig';
    }
}
