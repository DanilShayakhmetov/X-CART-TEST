<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\VendorMessages\View\ItemsList\Admin;

use XLite\Module\XC\VendorMessages\Model\Repo\Conversation as ConversationRepo;

/**
 * Conversations
 */
class Conversations extends \XLite\Module\XC\VendorMessages\View\ItemsList\Conversations
{
    /**
     * Widget param names
     */
    const PARAM_SEARCH_MESSAGES          = 'messages';
    const PARAM_SEARCH_MESSAGE_SUBSTRING = 'messageSubstring';

    /**
     * @inheritdoc
     */
    static public function getSearchParams()
    {
        return parent::getSearchParams() + [
            ConversationRepo::SEARCH_MESSAGES          => static::PARAM_SEARCH_MESSAGES,
            ConversationRepo::SEARCH_MESSAGE_SUBSTRING => static::PARAM_SEARCH_MESSAGE_SUBSTRING,
        ];
    }

    /**
     * @inheritdoc
     */
    protected function getSearchCondition()
    {
        $condition = parent::getSearchCondition();

        if (\XLite\Core\Auth::getInstance()->isPermissionAllowed('manage conversations')) {
            $condition->{ConversationRepo::P_MEMBER} = null;
        } elseif (\XLite\Core\Auth::getInstance()->isPermissionAllowed('manage orders')) {
            $condition->{ConversationRepo::P_MEMBER} = null;
            $condition->{ConversationRepo::P_ORDERS_ONLY} = true;
        }

        return $condition;
    }

    /**
     * @inheritdoc
     */
    protected function isHeadVisible()
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_SEARCH_MESSAGES          => new \XLite\Model\WidgetParam\TypeString('Messages type', ''),
            static::PARAM_SEARCH_MESSAGE_SUBSTRING => new \XLite\Model\WidgetParam\TypeString('Substring', ''),
        ];
    }

    /**
     * @inheritdoc
     */
    protected function getPagerClass()
    {
        return 'XLite\View\Pager\Admin\Model\Table';
    }

    /**
     * Check - marks conversation marks visible or not
     *
     * @param \XLite\Model\Order $order Order
     *
     * @return boolean
     */
    protected function isThreadsMultiple(\XLite\Model\Order $order)
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    protected function defineRequestParams()
    {
        parent::defineRequestParams();

        $this->requestParams[] = static::PARAM_SEARCH_MESSAGES;
        $this->requestParams[] = static::PARAM_SEARCH_MESSAGE_SUBSTRING;
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

                return $this->buildURL('order', '', [
                    'order_number' => $orderNumber,
                    'page'         => 'messages',
                    'recipient_id' => $order->getOrderId()
                ]);
            }

            return $this->buildURL('order', '', [
                'order_number' => $orderNumber,
                'page'         => 'messages'
            ]);
        }

        return $this->buildURL('conversation', '', ['id' => $conversation->getId()]);
    }

    /**
     * Check - order has opened dispute or not
     *
     * @param \XLite\Module\XC\VendorMessages\Model\Conversation $conversation
     *
     * @return boolean
     */
    protected function isOpenedDispute($conversation)
    {
        $result = false;

        if (
            \XLite\Module\XC\VendorMessages\Main::isMultivendor()
            && $conversation->getOrder()
        ) {
            $result = $conversation->getOrder()->getIsOpenedDispute();
        }

        return $result;
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
        return \XLite\Module\XC\VendorMessages\Main::isAllowDisputes();
    }

    /**
     * Get order url
     *
     * @param \XLite\Module\XC\VendorMessages\Model\Conversation $conversation
     *
     * @return string
     */
    protected function getOrderLink($conversation)
    {
        if ($order = $conversation->getOrder()) {
            $orderNumber = $order->getOrderNumber();
            if (!$orderNumber && \XLite\Module\XC\VendorMessages\Main::isMultivendor() && $order->isChild()) {
                $orderNumber = $order->getParent()->getOrderNumber();

                return $this->buildURL('order', '', [
                    'order_number' => $orderNumber,
                    'recipient_id' => $order->getOrderId()
                ]);
            }

            return $this->buildURL('order', '', [
                'order_number' => $orderNumber,
            ]);
        }

        return $this->buildURL('order_list', '', []);
    }

    /**
     * Get order number
     *
     * @param \XLite\Module\XC\VendorMessages\Model\Conversation $conversation
     *
     * @return string
     */
    protected function getOrderNumber($conversation)
    {
        $orderNumber = '';
        if ($order = $conversation->getOrder()) {
            $orderNumber = $order->getOrderNumber() ? $order->getPrintableOrderNumber() : null;
            if (!$orderNumber && \XLite\Module\XC\VendorMessages\Main::isMultivendor() && $order->isChild()) {
                $orderNumber = $order->getParent()->getPrintableOrderNumber();
            }
        }

        return $orderNumber;
    }
}