<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\VendorMessages\Controller\Customer;


class Conversation extends \XLite\Controller\Customer\ACustomer
{
    /**
     * Check if current page is accessible
     *
     * @return boolean
     */
    protected function checkAccess()
    {
        return parent::checkAccess() && $this->checkConversation();
    }

    /**
     * @return array
     */
    protected function getAllowedActions()
    {
        return [
            'contact_vendor'
        ];
    }

    /**
     * Check conversation if needed
     *
     * @return bool
     */
    protected function checkConversation()
    {
        return in_array($this->getAction(), $this->getAllowedActions())
               || ($this->getConversation() && $this->getConversation()->checkAccess());
    }

    /**
     * @inheritdoc
     */
    public function isSecure()
    {
        return \XLite\Core\Config::getInstance()->Security->customer_security;
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        $conversation = $this->getConversation();
        return $conversation && $conversation->checkAccess()
            ? $conversation->getName()
            : parent::getTitle();
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
    protected function getLocation()
    {
        return static::t('Conversation');
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
     * Find or create user-vendor conversation
     */
    protected function doActionContactVendor()
    {
        if (
            \XLite\Module\XC\VendorMessages\Main::isMultivendor()
            && \XLite\Module\XC\VendorMessages\Main::isVendorAllowedToCommunicate()
            && \XLite\Core\Request::getInstance()->vendor_id
        ) {
            $vendor = \XLite\Core\Database::getRepo('XLite\Model\Profile')
                ->find(\XLite\Core\Request::getInstance()->vendor_id);
            $profile = \XLite\Core\Auth::getInstance()->getProfile();


            if ($vendor && $profile && $vendor->getProfileId() !== $profile->getProfileId()) {
                $repo = \XLite\Core\Database::getRepo('XLite\Module\XC\VendorMessages\Model\Conversation');
                $conversation = $repo->findDialogue(
                    $profile,
                    $vendor
                );

                if (!$conversation) {
                    /** @var \XLite\Module\XC\VendorMessages\Model\Conversation $conversation */
                    $conversation = $repo->insert(null, false);
                    $conversation->addMember($profile);
                    $conversation->addMember($vendor);

                    \XLite\Core\Database::getEM()->flush($conversation);
                }
            }
        }

        if (!isset($conversation)) {
            $this->markAsAccessDenied();
        } else {
            $this->setReturnURL($this->buildURL('conversation', '', ['id' => $conversation->getId()]));
            $this->doRedirect();
        }
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