<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Reviews\Model\DTO\Settings\Notification;


use XLite\Model\Notification;

 class Customer extends \XLite\Model\DTO\Settings\Notification\CustomerAbstract implements \XLite\Base\IDecorator
{
    protected function init($object)
    {
        /* @var Notification $object */
        parent::init($object);

        if ($object->getTemplatesDirectory() === 'modules/XC/Reviews/review_key') {
            \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption([
                'category' => 'XC\\Reviews',
                'name' => 'enableCustomersFollowup',
                'value' => (boolean)$this->settings->status,
            ]);
        }
    }
}