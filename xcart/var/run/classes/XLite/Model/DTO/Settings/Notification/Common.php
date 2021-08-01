<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\DTO\Settings\Notification;

use XLite\Core\Translation;
use XLite\Model\DTO\Base\CommonCell;

class Common extends \XLite\Model\DTO\Base\ADTO
{
    /**
     * @param null $object
     */
    protected function init($object)
    {
        $customer = [
            'header'    => static::getValue('emailNotificationCustomerHeader'),
            'greeting'    => static::getValue('emailNotificationCustomerGreeting'),
            'signature' => static::getValue('emailNotificationCustomerSignature'),
        ];
        $this->customer = new CommonCell($customer);
        $admin = [
            'header'    => static::getValue('emailNotificationAdminHeader'),
            'greeting'    => static::getValue('emailNotificationAdminGreeting'),
            'signature' => static::getValue('emailNotificationAdminSignature'),
        ];
        $this->admin = new CommonCell($admin);
    }

    /**
     * @param null       $object
     * @param array|null $rawData
     *
     * @return mixed
     */
    public function populateTo($object, $rawData = null)
    {
        static::setValue('emailNotificationCustomerHeader', $rawData['customer']['header']);
        static::setValue('emailNotificationCustomerGreeting', $rawData['customer']['greeting']);
        static::setValue('emailNotificationCustomerSignature', $rawData['customer']['signature']);
        static::setValue('emailNotificationAdminHeader', $rawData['admin']['header']);
        static::setValue('emailNotificationAdminGreeting', $rawData['admin']['greeting']);
        static::setValue('emailNotificationAdminSignature', $rawData['admin']['signature']);
    }

    /**
     * @param string $field
     *
     * @return string
     */
    protected static function getValue($field)
    {
        return Translation::lbl($field);
    }

    /**
     * @param string $field
     * @param string $value
     */
    protected static function setValue($field, $value)
    {
        $label = \XLite\Core\Database::getRepo('\XLite\Model\LanguageLabel')->findOneByName($field);
        if ($label) {
            $label->setLabel($value);
        }
    }
}
