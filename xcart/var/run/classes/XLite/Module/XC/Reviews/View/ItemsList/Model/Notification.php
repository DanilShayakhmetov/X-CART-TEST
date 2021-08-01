<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Reviews\View\ItemsList\Model;


 class Notification extends \XLite\View\ItemsList\Model\NotificationAbstract implements \XLite\Base\IDecorator
{
    protected function updateEntities()
    {
        parent::updateEntities();

        foreach ($this->getPageDataForUpdate() as $notification) {
            /* @var \XLite\Model\Notification $notification */
            if ($notification->getTemplatesDirectory() === 'modules/XC/Reviews/review_key') {
                \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption([
                    'category' => 'XC\\Reviews',
                    'name' => 'enableCustomersFollowup',
                    'value' => (boolean)$notification->getEnabledForCustomer(),
                ]);
            }
        }
    }
}