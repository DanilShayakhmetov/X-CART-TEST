<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\VendorMessages\View\ItemsList\Messages\Base;

/**
 * Order messages
 */
abstract class Order extends \XLite\View\ItemsList\AItemsList
{
    /**
     * Author types
     */
    const AUTHOR_TYPE_CUSTOMER = 'customer';
    const AUTHOR_TYPE_ADMIN    = 'admin';

    /**
     * Widget param names
     */
    const PARAM_DISPLAY_ALL = 'display_all';

    /**
     * First read messages displayed (flag)
     *
     * @var boolean
     */
    protected $firstReadDisplayed = false;

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
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = $this->getPageBodyDir() . '/style.less';

        return $list;
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_DISPLAY_ALL => new \XLite\Model\WidgetParam\TypeBool(
                'Display all messages',
                false
            ),
        ];
    }

    /**
     * @inheritdoc
     */
    protected function finalizeTemplateDisplay($template, array $profilerData)
    {
        parent::finalizeTemplateDisplay($template, $profilerData);

        $this->markMessagesAsRead();
    }

    /**
     * @inheritdoc
     */
    protected function getPageBodyDir()
    {
        return 'modules/XC/VendorMessages/items_list/messages/order';
    }

    /**
     * @inheritdoc
     */
    protected function getPagerClass()
    {
        return '\XLite\View\Pager\Infinity';
    }

    /**
     * @inheritdoc
     */
    protected function getData(\XLite\Core\CommonCell $cnd, $countOnly = false)
    {
        return $countOnly
            ? count($this->getCurrentThreadOrder()->getMessages())
            : $this->getCurrentThreadOrder()->getMessages();
    }

    /**
     * Get first data
     *
     * @return \XLite\Module\XC\VendorMessages\Model\Message[]
     */
    protected function getFirstData()
    {
        $messages = $this->getPageData();

        return ($messages && count($messages) > 0)
            ? [$messages[0]]
            : null;
    }

    /**
     * Get last data
     *
     * @return \XLite\Module\XC\VendorMessages\Model\Message[]
     */
    protected function getLastData()
    {
        $result = [];

        $found = false;
        $messages = $this->getPageData();
        foreach ($messages as $message) {
            if (!$found && !$message->isRead($this->getProfile())) {
                $found = true;
            }

            if ($found) {
                $result[] = $message;
            }
        }

        if (!$result && !empty($message)) {
            $result[] = $message;
        }

        return $result;
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
     * @inheritdoc
     */
    protected function defineRequestParams()
    {
        parent::defineRequestParams();

        $this->requestParams[] = static::PARAM_DISPLAY_ALL;
    }

    /**
     * Mark messages as read
     *
     * @return integer
     */
    protected function markMessagesAsRead()
    {
        $count = 0;
        foreach ($this->getPageData() as $message) {
            if (!$message->isRead($this->getProfile())) {
                $read = $message->markAsRead($this->getProfile());
                if ($read) {
                    \XLite\Core\Database::getEM()->persist($read);
                    $count++;
                }
            }
        }

        if ($count > 0) {
            \XLite\Core\Database::getEM()->flush();
        }

        return $count;
    }

    // {{{ Content helpers

    /**
     * Get message line tag attributes
     *
     * @param \XLite\Module\XC\VendorMessages\Model\Message $message
     *
     * @return array
     */
    protected function getLineTagAttributes(\XLite\Module\XC\VendorMessages\Model\Message $message)
    {
        $attributes = [
            'class' => ['message'],
        ];

        $attributes['class'][] = $message->isRead($this->getProfile()) ? 'read' : 'unread';
        $attributes['class'][] = $message->isOwner() ? 'owner' : 'non-owner';
        $attributes['class'][] = 'author-' . $this->getAuthorType($message);

        return $attributes;
    }

    /**
     * Get author type
     *
     * @param \XLite\Module\XC\VendorMessages\Model\Message $message
     *
     * @return string
     */
    protected function getAuthorType($message)
    {
        return $message->getAuthor()->isAdmin()
            ? static::AUTHOR_TYPE_ADMIN
            : static::AUTHOR_TYPE_CUSTOMER;
    }

    /**
     * Count hidden messages
     *
     * @return integer
     */
    protected function countHiddenMessages()
    {
        return $this->getItemsCount() > 5
            ? max(0, $this->getItemsCount() - count($this->getFirstData()) - count($this->getLastData()))
            : 0;
    }

    /**
     * Count unread messages
     *
     * @return integer
     */
    protected function countUnreadMessages()
    {
        return count($this->getLastData());
    }

    /**
     * Check - display all messages or not
     *
     * @return boolean
     */
    protected function isDisplayAll()
    {
        return $this->getParam(static::PARAM_DISPLAY_ALL) || $this->countHiddenMessages() == 0;
    }

    /**
     * Check - separator (closed) is visible or not
     *
     * @return boolean
     */
    protected function isClosedSeparatorVisible()
    {
        return $this->countHiddenMessages() > 0;
    }

    /**
     * Check - separator (opened) is visible or not
     *
     * @return boolean
     */
    protected function isOpenedSeparatorVisible()
    {
        return $this->countHiddenMessages() > 0;
    }

    /**
     * Return profile
     *
     * @return mixed
     */
    protected function getProfile()
    {
        return \XLite\Core\Auth::getInstance()->getProfile() ?: $this->getOrderProfile();
    }

    /**
     * Return order profile
     *
     * @return mixed
     */
    protected function getOrderProfile()
    {
        return $this->getCurrentThreadOrder()->getOrigProfile();
    }

    /**
     * Check - 'New messages' separator visible or not
     *
     * @param \XLite\Module\XC\VendorMessages\Model\Message $message Message
     *
     * @return boolean
     */
    protected function isNewSeparatorVisible(\XLite\Module\XC\VendorMessages\Model\Message $message)
    {
        $visible = !$this->firstReadDisplayed && !$message->isRead($this->getProfile());
        if ($visible) {
            $this->firstReadDisplayed = true;
        }

        return $visible;
    }

    /**
     * Check - dispute is allow or not
     *
     * @return boolean
     */
    protected function isAllowDispute()
    {
        return false;
    }

    /**
     * Check - dispute is open or not
     *
     * @return boolean
     */
    protected function isOpenedDispute()
    {
        return false;
    }

    /**
     * Check - 'Dispute is open' note is visible or not
     *
     * @return boolean
     */
    protected function isOpenedDisputeNoteVisible()
    {
        return $this->isOpenedDispute() && \XLite::isAdminZone();
    }

    /**
     * Check - author email is visible or not
     *
     * @param \XLite\Module\XC\VendorMessages\Model\Message $message Message
     *
     * @return boolean
     */
    protected function isEmailVisible(\XLite\Module\XC\VendorMessages\Model\Message $message)
    {
        return !$message->getAuthor()->isAdmin()
               && \XLite::isAdminZone();
    }

    /**
     * @param \XLite\Module\XC\VendorMessages\Model\Message $message
     *
     * @return mixed
     */
    protected function getEmail(\XLite\Module\XC\VendorMessages\Model\Message $message)
    {
        return $message->getAuthorEmail();
    }

    /**
     * Check - recipient selector is visible or not
     *
     * @return boolean
     */
    protected function isRecipientSelectorVisible()
    {
        return false;
    }

    /**
     * Get recipients list
     *
     * @return array
     */
    protected function getRecipients()
    {
        return [
            0 => \XLite\Core\Config::getInstance()->Company->company_name,
        ];
    }

    /**
     * Get new message class
     *
     * @return string
     */
    public function getNewMessageClass()
    {
        return '';
    }

    // }}}
}
