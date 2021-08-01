<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\VendorMessages\View\ItemsList;
use XLite\Module\XC\VendorMessages\Model\Repo\Conversation as ConversationRepo;


/**
 * Conversations items-list
 */
class Conversations extends \XLite\View\ItemsList\AItemsList
{
    /**
     * Get repository
     *
     * @return ConversationRepo
     */
    protected function getRepository()
    {
        return \XLite\Core\Database::getRepo('XLite\Module\XC\VendorMessages\Model\Conversation');
    }

    /**
     * @inheritdoc
     */
    static public function getSearchParams()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    protected function getPagerClass()
    {
        return '\XLite\Module\XC\VendorMessages\View\Pager\Conversations';
    }

    /**
     * @inheritdoc
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = $this->getPageBodyDir() . '/style.less';

        return $list;
    }

    /**
     * @inheritdoc
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = $this->getPageBodyDir() . '/controller.js';

        return $list;
    }

    /**
     * @inheritdoc
     */
    public function getListCSSClasses()
    {
        return parent::getListCSSClasses() . ' conversations';
    }

    /**
     * @inheritdoc
     */
    protected function isPagerVisible()
    {
        return parent::isPagerVisible()
               && $this->getItemsCount() > 0;
    }

    /**
     * @inheritdoc
     */
    protected function getPageBodyDir()
    {
        return 'modules/XC/VendorMessages/items_list/conversations';
    }

    /**
     * @inheritdoc
     */
    protected function getSearchCondition()
    {
        $condition = parent::getSearchCondition();

        foreach (static::getSearchParams() as $modelParam => $requestParam) {
            $paramValue = $this->getParam($requestParam);

            if ('' !== $paramValue && 0 !== $paramValue) {
                $condition->$modelParam = $paramValue;
            }
        }

        $condition->{ConversationRepo::P_MEMBER} = \XLite\Core\Auth::getInstance()->getProfile();

        $condition->{ConversationRepo::P_ORDER_BY} = [
            'read_messages',
            'asc',
            \XLite\Core\Auth::getInstance()->getProfile(),
        ];

        $condition->{ConversationRepo::P_ORDERS_CONDITIONS} = true;

        return $condition;
    }

    /**
     * @inheritdoc
     */
    protected function getData(\XLite\Core\CommonCell $cnd, $countOnly = false)
    {
        return $this->getRepository()->search($cnd, $countOnly);
    }

    /**
     * @inheritdoc
     */
    protected function isDisplayWithEmptyList()
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    protected function getPageBodyTemplate()
    {
        return $this->getPageBodyDir() . LC_DS . $this->getPageBodyFile();
    }

    /**
     * @inheritdoc
     */
    protected function getEmptyListTemplate()
    {
        return $this->getPageBodyDir() . LC_DS . $this->getEmptyListFile();
    }

    /**
     * Get last message
     *
     * @param \XLite\Module\XC\VendorMessages\Model\Conversation $conversation
     *
     * @return \XLite\Module\XC\VendorMessages\Model\Message
     */
    protected function getLastMessage($conversation)
    {
        return $conversation->getLastMessage();
    }

    /**
     * Get message line tag attributes
     *
     * @param \XLite\Module\XC\VendorMessages\Model\Conversation $conversation
     *
     * @return array
     */
    protected function getLineTagAttributes($conversation)
    {
        $message = $this->getLastMessage($conversation);

        $attributes = [
            'class' => ['message'],
        ];

        $attributes['class'][] = !$message || $message->isRead() ? 'read' : 'unread';

        if ($this->isMarksVisible($conversation)) {
            $attributes['class'][] = 'has-marks';
        }

        return $attributes;
    }

    /**
     * Prepare body
     *
     * @param \XLite\Module\XC\VendorMessages\Model\Conversation $conversation
     *
     * @return string
     */
    protected function prepareBody($conversation)
    {
        $message = $this->getLastMessage($conversation);

        return $message ? $message->getBody() : static::t('n/a');
    }

    /**
     * Prepare time
     *
     * @param \XLite\Module\XC\VendorMessages\Model\Conversation $conversation
     *
     * @return string
     */
    protected function prepareTime($conversation)
    {
        $message = $this->getLastMessage($conversation);

        return $message ? $message->getDate() : 0;
    }

    /**
     * Get row label
     *
     * @param \XLite\Module\XC\VendorMessages\Model\Conversation $conversation
     *
     * @return string
     */
    protected function getLabel($conversation)
    {
        $count = $conversation->countUnreadMessages();

        if (!$count) {
            return '';
        }

        if ($conversation->getOrder()) {
            return $count > 1
                ? static::t('X new message for order', ['count' => $count])
                : static::t('New message for order');
        }

        return $count > 1
            ? static::t('X new messages', ['count' => $count])
            : static::t('New message');
    }

    /**
     * Get conversation url
     *
     * @param \XLite\Module\XC\VendorMessages\Model\Conversation $conversation
     *
     * @return string
     */
    protected function getConversationLink($conversation)
    {
        if ($order = $conversation->getOrder()) {
            $orderNumber = $order->getOrderNumber();
            if (!$orderNumber && \XLite\Module\XC\VendorMessages\Main::isMultivendor() && $order->isChild()) {
                $orderNumber = $order->getParent()->getOrderNumber();

                return $this->buildURL('order_messages', '', [
                    'order_number' => $orderNumber,
                    'recipient_id' => $order->getOrderId()
                ]);
            }

            return $this->buildURL('order_messages', '', ['order_number' => $orderNumber]);
        }

        return $this->buildURL('conversation', '', ['id' => $conversation->getId()]);
    }

    /**
     * Check - conversation marks visible or not
     *
     * @param \XLite\Module\XC\VendorMessages\Model\Conversation $conversation
     *
     * @return boolean
     */
    protected function isMarksVisible($conversation)
    {
        return false;
    }
}