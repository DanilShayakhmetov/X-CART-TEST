<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\Amazon\PayWithAmazon\Core\Mail;


use XLite\Model\Order;
use XLite\View\Mailer;

class UpdateAmazonPaymentInfo extends \XLite\Core\Mail\AMail
{
    protected static $amazonDomains = [
        'us' => '.com',
        'uk' => '.co.uk',
        'de' => '.de',
        'fr' => '.fr',
        'it' => '.it',
        'es' => '.es',
    ];

    static function getInterface()
    {
        return \XLite::CUSTOMER_INTERFACE;
    }

    static function getDir()
    {
        return 'modules/Amazon/PayWithAmazon/update_payment_info';
    }

    public function __construct(Order $order)
    {
        parent::__construct();

        $profile = $order->getProfile();

        $method = \XLite\Module\Amazon\PayWithAmazon\Main::getMethod();
        $region = \XLite\Module\Amazon\PayWithAmazon\View\FormField\Select\Region::getRegionByCurrency($method->getSetting('region'));

        $domain = isset(static::$amazonDomains[$region]) ? static::$amazonDomains[$region] : static::$amazonDomains['us'];

        $amazonLink = str_replace('{%domain%}', $domain, 'https://payments.amazon{%domain%}/jr/your-account/orders?language=en_GB');

        $this->setFrom(\XLite\Core\Mailer::getOrdersDepartmentMail());
        $this->setTo(['email' => $profile->getLogin(), 'name' => $profile->getName(false)]);
        $this->setReplyTo(\XLite\Core\Mailer::getOrdersDepartmentMails());

        $this->populateVariables(
            [
                'recipient_name'  => $order->getProfile()->getName(),
            ]
        );

        $this->appendData(
            [
                'orderNumber'     => $order->getOrderNumber(),
                'amazonLink'      => $amazonLink,
            ]
        );
    }

    /**
     * @return bool
     */
    public static function isEnabled()
    {
        return true;
    }
}