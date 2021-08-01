<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Dashboard\Admin\InfoBlock\Notification;

use XLite;
use XLite\Core\Auth;
use XLite\Core\Cache\ExecuteCachedTrait;
use XLite\Core\Marketplace;
use XLite\Core\URLManager;

/**
 * @ListChild (list="dashboard.info_block.notifications", weight="100", zone="admin")
 */
class MarketplaceWarning extends \XLite\View\Dashboard\Admin\InfoBlock\ANotification
{
    use ExecuteCachedTrait;

    /**
     * @return string
     */
    protected function getNotificationType()
    {
        return 'marketplaceWarning';
    }

    /**
     * @return string
     */
    protected function getClass()
    {
        return parent::getClass() . ' marketplace-warning';
    }

    /**
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'dashboard/info_block/notification/marketplace_warning.twig';
    }

    /**
     * @return string
     */
    protected function getHeader()
    {
        return static::t('Security issue');
    }

    /**
     * @return bool
     */
    protected function isVisible()
    {
        return parent::isVisible() && $this->getMessages();
    }

    /**
     * Last read timestamp
     *
     * @return integer
     */
    protected function getLastReadTimestamp()
    {
        return -1;
    }

    protected function getLastUpdateTimestamp()
    {
        return 1;
    }

    /**
     * Get urgent messages
     *
     * @return array
     */
    protected function getMessages()
    {
        return $this->executeCachedRuntime(function () {
            $messages = array_map([$this, 'parseMessage'], $this->fetchMessages());
            $messages = array_filter($messages, [$this, 'filterMessage']);
            usort($messages, [$this, 'sortMessages']);

            return $messages;
        });

    }

    /**
     * Fetch messages
     *
     * @return array
     */
    protected function fetchMessages()
    {
        $result = [];

        $messages = Marketplace::getInstance()->getXC5Notifications();
        foreach ($messages as $message) {
            if ($message['type'] === 'warning') {
                $result[] = $message;
            }
        }

        return $result;
    }

    /**
     * Parse message
     *
     * @param array $message Message
     *
     * @return array
     */
    protected function parseMessage($message)
    {
        if ($message['link']) {
            $params          = [
                'utm_source'   => 'xc5admin',
                'utm_medium'   => 'link2blog',
                'utm_campaign' => 'xc5adminlink2blog',
            ];
            $message['link'] = URLManager::appendParamsToUrl($message['link'], $params);
        }

        return $message;
    }

    /**
     * Parse message
     *
     * @param array $message Message
     *
     * @return bool
     */
    protected function filterMessage($message)
    {
        $messageTimestamp = $message['date'] ?? 0;

        if ($messageTimestamp) {
            $readTimestamp = (int) (\XLite\Core\Request::getInstance()->{'marketplaceWarning' . $messageTimestamp . 'ReadTimestamp'} ?? 0);

            return $readTimestamp < $messageTimestamp;
        }

        return false;
    }

    /**
     * Sort helper
     *
     * @param array $a First message
     * @param array $b Second message
     *
     * @return boolean
     */
    protected function sortMessages($a, $b)
    {
        return isset($a['date'], $b['date']) && $a['date'] < $b['date'];
    }

    /**
     * @param array $message
     *
     * @return array
     */
    protected function getMessageTagAttributes($message)
    {
        $result                           = $this->getTagAttributes();
        $result['data-notification-type'] .= $message['date'];

        return $result;
    }

    /**
     * @param array $message
     *
     * @return string
     */
    protected function getMessageHeaderUrl($message)
    {
        return $message['link'] ?? '';
    }

    /**
     * @param array $message
     *
     * @return string
     */
    protected function getMessageHeader($message)
    {
        return $message['title'] ?? '';
    }

    /**
     * @param array $message
     *
     * @return string
     */
    protected function getMessageBody($message)
    {
        return $message['description'] ?? '';
    }

    /**
     * @return bool
     */
    protected function isExternal()
    {
        return true;
    }

    /**
     * @return bool
     */
    protected function checkACL()
    {
        return parent::checkACL()
            && Auth::getInstance()->hasRootAccess();
    }
}
