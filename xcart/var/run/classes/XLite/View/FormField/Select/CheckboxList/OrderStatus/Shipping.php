<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select\CheckboxList\OrderStatus;

use XLite\Core\Cache\ExecuteCachedTrait;

/**
 * Shipping order status selector
 */
class Shipping extends \XLite\View\FormField\Select\CheckboxList\ACheckboxList
{
    use ExecuteCachedTrait;

    protected function getShippingStatuses()
    {
        return $this->executeCachedRuntime(function() {
            $statuses = [];
            foreach (\XLite\Core\Database::getRepo('XLite\Model\Order\Status\Shipping')->findBy(array(), array('position' => 'asc')) as $status) {
                $statuses[$status->getId()] = $status;
            };

            return $statuses;
        });
    }

    /**
     * Set value
     *
     * @param mixed $value Value to set
     *
     * @return void
     */
    public function setValue($value)
    {
        $shippingStatuses = $this->getShippingStatuses();
        if (is_array($value)) {
            foreach ($value as $k => $v) {
                if (!is_object($v) && isset($shippingStatuses[$v])) {
                    $value[$k] = $shippingStatuses[$v];
                }
            }
        }

        parent::setValue($value);
    }

    /**
     * Return default options list
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        $list = array();

        foreach ($this->getShippingStatuses() as $status) {
            $list[$status->getId()] = $status->getName();
        }

        return $list;
    }

    /**
     * Set common attributes
     *
     * @param array $attrs Field attributes to prepare
     *
     * @return array
     */
    protected function setCommonAttributes(array $attrs)
    {
        $list = parent::setCommonAttributes($attrs);
        $list['data-placeholder'] = static::t('All shipping statuses');

        return $list;
    }

}
