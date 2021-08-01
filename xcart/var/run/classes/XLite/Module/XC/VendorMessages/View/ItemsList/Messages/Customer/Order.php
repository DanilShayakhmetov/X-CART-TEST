<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\VendorMessages\View\ItemsList\Messages\Customer;

/**
 * Customer order messages
 */
class Order extends \XLite\Module\XC\VendorMessages\View\ItemsList\Messages\Base\Order
{
    /**
     * @inheritdoc
     */
    public function getListCSSClasses()
    {
        $classes = parent::getListCSSClasses() . ' order-messages';

        if ($this->isRecipientSelectorVisible()) {
            $classes .= ' recipient-selector-visible';

        } else {
            $classes .= ' recipient-selector-invisible';
        }

        return $classes;
    }

    /**
     * @inheritdoc
     */
    protected static function getWidgetTarget()
    {
        return 'order_messages';
    }

    /**
     * @inheritdoc
     */
    protected function getWidgetParameters()
    {
        return [
            'order_number' => $this->getOrder()->getOrderNumber(),
        ];
    }

    /**
     * Get URL common parameters
     *
     * @return array
     */
    protected function getCommonParams()
    {
        $initialize = !isset($this->commonParams);

        $this->commonParams = parent::getCommonParams();

        if ($initialize) {
            $this->commonParams += [
                'order_number' => $this->getOrder()->getOrderNumber(),
            ];
        }

        return $this->commonParams;
    }

    /**
     * Get open URL
     *
     * @return string
     */
    protected function getOpenURL()
    {
        return static::buildURL(
            'order_messages',
            null,
            $this->getWidgetParameters() + [
                'display_all' => 1,
            ]
        );
    }

    /**
     * Get close URL
     *
     * @return string
     */
    protected function getCloseURL()
    {
        return static::buildURL(
            'order_messages',
            null,
            $this->getWidgetParameters() + [
                'display_all' => 0,
            ]
        );
    }

    // }}}
}
