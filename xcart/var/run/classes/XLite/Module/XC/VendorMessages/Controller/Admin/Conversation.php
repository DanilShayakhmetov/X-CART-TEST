<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\VendorMessages\Controller\Admin;


class Conversation extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Check ACL permissions
     *
     * @return boolean
     */
    public function checkACL()
    {
        return true;
    }

    /**
     * Check if current page is accessible
     *
     * @return boolean
     */
    public function checkAccess()
    {
        return parent::checkAccess() && $this->getConversation() && $this->getConversation()->checkAccess();
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->getConversation()->getName();
    }

    /**
     * Return current conversation Id
     *
     * @return integer
     */
    public function getConversationId()
    {
        return \XLite\Core\Request::getInstance()->id;
    }

    /**
     * Return current conversation
     *
     * @return \XLite\Module\XC\VendorMessages\Model\Conversation
     */
    public function getConversation()
    {
        return \XLite\Core\Database::getRepo('XLite\Module\XC\VendorMessages\Model\Conversation')
            ->find($this->getConversationId());
    }

    /**
     * @inheritdoc
     */
    protected function addBaseLocation()
    {
        parent::addBaseLocation();

        $this->addLocationNode(static::t('Messages'), $this->buildURL('messages'));
    }

    /**
     * Create new message
     */
    protected function doActionCreate()
    {
        $errors = $this->checkMessageBeforeCreate();
        if (empty($errors)) {
            \XLite\Core\Database::getRepo('XLite\Module\XC\VendorMessages\Model\Message')->insert($this->createNewMessage());
            \XLite\Core\Event::conversationMessageCreated();
        } else {
            $this->valid = false;
            foreach ($errors as $error) {
                \XLite\Core\TopMessage::addError($error);
            }
        }
    }

    /**
     * Check message
     *
     * @return array
     */
    protected function checkMessageBeforeCreate()
    {
        $errors = [];
        $request = \XLite\Core\Request::getInstance();

        if (!isset($request->body) || !strlen($request->body)) {
            $errors[] = 'The field Body may not be blank';
        }

        return $errors;
    }

    /**
     * Create new message
     *
     * @return \XLite\Module\XC\VendorMessages\Model\Message
     */
    protected function createNewMessage()
    {
        $request = \XLite\Core\Request::getInstance();

        /** @var \XLite\Module\XC\VendorMessages\Model\Message $message */
        $message = $this->getConversation()->buildNewMessage(
            \XLite\Core\Auth::getInstance()->getProfile(),
            $request->body
        );

        return $message;
    }
}