<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Menu\Admin;

use XLite\Core\Cache\ExecuteCached;

class SelectedDecider implements \Serializable
{
    /**
     * @var array
     */
    private $items;
    private $itemsProcessed;
    private $selectedItem;
    /**
     * @var \XLite\View\Menu\Admin\AAdmin
     */
    private $controller;
    /**
     * @var
     */
    private $menuClassName;
    /**
     * @var
     */
    private $getter;

    /**
     * SelectedDecider constructor.
     *
     * @param $menuClassName
     * @param $getter
     */
    public function __construct($menuClassName, $getter)
    {
        $this->menuClassName = $menuClassName;
        $this->getter = $getter;
    }

    public function isSelected($target, $name)
    {
        $this->prepare($target);

        return isset($this->itemsProcessed[$name]['decider_selected'])
            && $this->itemsProcessed[$name]['decider_selected'] === true;
    }

    public function isExpanded($target, $name)
    {
        $this->prepare($target);

        return isset($this->itemsProcessed[$name]['decider_expanded'])
            && $this->itemsProcessed[$name]['decider_expanded'] === true;
    }

    protected function prepare($target)
    {
        $cacheParams = [
            $target,
            'prepareItems',
            $this->getter,
            $this->menuClassName
        ];

        $this->itemsProcessed = ExecuteCached::executeCachedRuntime(function() use ($target) {
            $this->controller = new $this->menuClassName;
            $this->items = $this->controller->{$this->getter}();
            $this->findAndSetSelectedItem($target);
            return $this->markSelected($this->items);
        }, $cacheParams);

        return $this->itemsProcessed;
    }

    /**
     * @param $items
     */
    protected function findAndSetSelectedItem($target, $items = null)
    {
        if (!$items) {
            $items = $this->items;
        }

        $request = \XLite\Core\Request::getInstance();

        foreach ($items as $index => $item) {
            if (isset($item[AAdmin::ITEM_CHILDREN])
                && is_array($item[AAdmin::ITEM_CHILDREN])
                && !empty($item[AAdmin::ITEM_CHILDREN])
            ) {
                $this->findAndSetSelectedItem($target, $item[AAdmin::ITEM_CHILDREN]);
            }

            if (isset($item[AAdmin::ITEM_TARGET])) {
                $weight = $this->controller->getTargetWeight($target, $item[AAdmin::ITEM_TARGET], $item[AAdmin::ITEM_EXTRA] ?? []);
                $selected = $weight > 0;

                if ($selected
                    && (empty($this->selectedItem)
                        || $weight > $this->selectedItem['weight']
                    )
                ) {
                    $this->selectedItem = [
                        'weight' => $weight,
                        'index'  => $index,
                    ];
                }
            }
        }
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
        $registry = [];

        if (!empty($this->selectedItem)
            && $items
        ) {
            foreach ($items as $index => $item) {
                if ($index == $this->selectedItem['index']) {
                    $registry[$index]['decider_selected'] = true;
                    break;

                }

                if (isset($item[AAdmin::ITEM_CHILDREN]) && $item[AAdmin::ITEM_CHILDREN]) {
                    $children = $this->markSelected($item[AAdmin::ITEM_CHILDREN]);
                    $registry = array_merge($registry, $children);

                    $result = false;
                    foreach ($children as $child) {
                        if ((isset($child['decider_selected']) && $child['decider_selected'])
                            || (isset($child['decider_expanded']) && $child['decider_expanded'])
                        ) {
                            $result = true;
                            break;
                        }

                    }

                    if ($result) {
                        $registry[$index]['decider_expanded'] = true;
                    }
                }
            }
        }

        return $registry;
    }

    /**
     * String representation of object
     * @link  http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     * @since 5.1.0
     */
    public function serialize()
    {
        return serialize([ $this->menuClassName, $this->getter ]);
    }

    /**
     * Constructs the object
     * @link  http://php.net/manual/en/serializable.unserialize.php
     *
     * @param string $serialized <p>
     *                           The string representation of the object.
     *                           </p>
     *
     * @return void
     * @since 5.1.0
     */
    public function unserialize($serialized)
    {
        list($this->menuClassName, $this->getter) = unserialize($serialized);
    }
}
