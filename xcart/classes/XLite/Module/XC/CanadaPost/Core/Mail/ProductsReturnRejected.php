<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CanadaPost\Core\Mail;


use XLite\Core\Mailer;
use XLite\Module\XC\CanadaPost\Model\ProductsReturn;
use XLite\Module\XC\CanadaPost\Model\ProductsReturn\Item;

class ProductsReturnRejected extends \XLite\Core\Mail\AMail
{
    static function getInterface()
    {
        return \XLite::CUSTOMER_INTERFACE;
    }

    static function getDir()
    {
        return 'modules/XC/CanadaPost/return_rejected';
    }

    public function __construct(ProductsReturn $return)
    {
        parent::__construct();

        if (
            $return->getOrder()
            && $profile = $return->getOrder()->getProfile()
        ) {
            $this->setFrom(Mailer::getOrdersDepartmentMail());
            $this->setTo(['email' => $profile->getLogin(), 'name' => $profile->getName(false)]);
            $this->setReplyTo(Mailer::getOrdersDepartmentMails());
            $this->tryToSetLanguageCode($profile->getLanguage());

            $this->appendData([
                'productsReturn' => $return,
                'notes'          => nl2br($return->getAdminNotes(), false),
                'products'       => array_map(function (Item $item) {
                    return $item->getOrderItem()->getProduct();
                }, $return->getItems()->toArray()),
            ]);
        }
    }

    public function schedule()
    {
        return !empty($this->getData()['productsReturn'])
            ? parent::schedule()
            : false;
    }

    public function send()
    {
        return !empty($this->getData()['productsReturn'])
            ? parent::send()
            : false;
    }
}