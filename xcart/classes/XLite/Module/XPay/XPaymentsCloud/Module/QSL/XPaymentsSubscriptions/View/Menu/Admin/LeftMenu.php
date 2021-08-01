<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XPay\XPaymentsCloud\Module\QSL\XPaymentsSubscriptions\View\Menu\Admin;

/**
 * Left menu widget
 * @Decorator\Depend({"QSL\XPaymentsSubscriptions", "XPay\XPaymentsCloud"})
 */
class LeftMenu extends \XLite\View\Menu\Admin\LeftMenu implements \XLite\Base\IDecorator
{
    /**
     * @inheritDoc
     */
    protected function defineItems()
    {
        $list = parent::defineItems();

        if (isset($list['sales'][static::ITEM_CHILDREN]['x_payments_subscription'])) {
            $list['sales'][static::ITEM_CHILDREN]['x_payments_subscription'][static::ITEM_TITLE]
                = static::t('Subscriptions (legacy)');
        }

        return $list;
    }

}
