<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Egoods\Module\XC\ThemeTweaker\Core\Notifications\Data;


/**
 * Order
 *
 * @Decorator\Depend("XC\ThemeTweaker")
 */
class Order extends \XLite\Module\XC\ThemeTweaker\Core\Notifications\Data\Order implements \XLite\Base\IDecorator
{
    protected function getTemplateDirectories()
    {
        return array_merge(parent::getTemplateDirectories(), [
            'modules/CDev/Egoods/egoods_links',
        ]);
    }

    public function getSuitabilityErrors($templateDir)
    {
        $errors = parent::getSuitabilityErrors($templateDir);

        /** @var \XLite\Module\CDev\Egoods\Model\Order $order */
        $order = $this->getOrder($templateDir);

        if (
            $templateDir === 'modules/CDev/Egoods/egoods_links'
            && $order
            && !$order->getDownloadAttachments()
        ) {
            $errors[] = [
                'code'  => 'no_egoods',
                'value' => $order->getOrderNumber(),
                'type'  => 'warning'
            ];
        }

        return $errors;
    }
}