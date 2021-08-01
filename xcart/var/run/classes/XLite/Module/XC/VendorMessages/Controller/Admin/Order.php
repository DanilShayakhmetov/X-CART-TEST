<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\VendorMessages\Controller\Admin;

/**
 * Order page controller
 */
 class Order extends \XLite\Module\XPay\XPaymentsCloud\Controller\Admin\Order implements \XLite\Base\IDecorator
{
    /**
     * @inheritdoc
     */
    public function getPages()
    {
        $list = parent::getPages();

        if ($this->getOrder()) {
            $list['messages'] = [
                'title'        => static::t('Messages'),
                'linkTemplate' => 'modules/XC/VendorMessages/order/page/messages_link.twig',
            ];
        }

        if (\Includes\Utils\Module\Manager::getRegistry()->isModuleEnabled('XC', 'NotFinishedOrders')
            && $this->getOrder()
            && $this->getOrder()->isNotFinishedOrder()
        ) {
            unset($list['messages']);
        }

        return $list;
    }

    /**
     * Count unread messages
     *
     * @return integer
     */
    public function countUnreadMessages()
    {
        return $this->getOrder()->countUnreadMessages();
    }

    /**
     * Get current thread order
     *
     * @return \XLite\Model\Order
     */
    public function getCurrentThreadOrder()
    {
        return $this->getOrder();
    }

    /**
     * @inheritdoc
     */
    protected function getPageTemplates()
    {
        $list = parent::getPageTemplates();
        $list['messages'] = 'modules/XC/VendorMessages/order/page/messages.twig';

        return $list;
    }

    // {{{ Actions

    /**
     * Update messages list
     */
    protected function doActionUpdateMessages()
    {
        $this->restoreFormId();

        if ($this->needCreateNewMessage()) {
            \XLite\Core\Database::getRepo('XLite\Module\XC\VendorMessages\Model\Message')->insert($this->createNewMessage());
            \XLite\Core\Event::orderMessagesCreate();
        }
    }

    /**
     * Check - need create new message or not
     *
     * @return boolean
     */
    protected function needCreateNewMessage()
    {
        $request = \XLite\Core\Request::getInstance();

        if (!$request->body) {
            \XLite\Core\TopMessage::addError('The field Body may not be blank');
            $this->valid = false;
        }

        return (bool)$request->body;
    }

    /**
     * Create new message
     *
     * @return \XLite\Module\XC\VendorMessages\Model\Message
     */
    protected function createNewMessage()
    {
        $request = \XLite\Core\Request::getInstance();

        $message = $this->getCurrentThreadOrder()->buildNewMessage(
            \XLite\Core\Auth::getInstance()->getProfile(),
            $request->body
        );
        $message->resetNotifications();

        return $message;
    }

    // }}}

}
