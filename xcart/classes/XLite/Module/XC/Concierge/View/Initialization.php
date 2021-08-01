<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Concierge\View;

use XLite\Core\Cache\ExecuteCached;
use XLite\Core\Cache\ExecuteCachedTrait;
use XLite\Core\HTTP\Request;
use XLite\Module\XC\Concierge\Core\AMessage;
use XLite\Module\XC\Concierge\Core\Message\Page;

/**
 * Initialization
 *
 * @ListChild (list="head", zone="admin")
 */
class Initialization extends \XLite\View\AView
{
    use ExecuteCachedTrait;

    const INTERCOM_HASH_URL = 'https://mc-end-auth.qtmsoft.com/intercom_hash.php';

    /**
     * Return list of disallowed targets
     *
     * @return string[]
     */
    public static function getDisallowedTargets()
    {
        return [
            'login',
            'recover_password',
        ];
    }

    /**
     * @inheritdoc
     */
    public function getJSFiles()
    {
        $list   = parent::getJSFiles();
        $list[] = 'modules/XC/Concierge/head.js';

        return $list;
    }

    /**
     * @inheritdoc
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && $this->getMediator()->getWriteKey()
            && $this->getMediator()->getUserId();
    }

    /**
     * @inheritdoc
     */
    protected function getDefaultTemplate()
    {
        return 'modules/XC/Concierge/head.twig';
    }

    /**
     * Get mediator
     *
     * @return \XLite\Module\XC\Concierge\Core\Mediator
     */
    protected function getMediator()
    {
        return \XLite\Module\XC\Concierge\Core\Mediator::getInstance();
    }

    /**
     * Get messages
     *
     * @return array
     */
    protected function getMessages()
    {
        return $this->executeCachedRuntime(function () {
            return $this->prepareMessages($this->defineMessages());
        });
    }

    /**
     * @return array
     */
    protected function defineMessages()
    {
        $controller = \XLite::getController();

        $pageMessage = new Page($controller->getConciergeCategory(), $controller->getConciergeTitle());
        $pageMessage->setIntegrations($this->defineIntegrations());
        $pageMessages = [$pageMessage->toArray()];

        $identifyMessages = $this->getMediator()->getIdentifyMessage();
        if ($identifyMessages && !empty($identifyMessages[0])) {
            $identifyMessages[0]->setIntegrations(['Intercom' => false]);
        }

        if (\XLite\Core\Auth::getInstance()->hasRootAccess()) {
            $pageMessageIntercom = clone $pageMessage;
            $pageMessageIntercom->setIntegrations(['All' => false, 'Intercom' => true]);
            $pageMessages = array_merge($pageMessages, [$pageMessageIntercom->toArray('intercom')]);

            if ($identifyMessages && !empty($identifyMessages[0])) {
                $identifyMessages[0]->setIntegrations(['Intercom' => ['user_hash' => $this->getIntercomHash()]]);
            }
        }

        $t = array_merge(
            $identifyMessages,
            $pageMessages,
            $this->getMediator()->getMessages()
        );

        return $t;
    }

    /**
     * @return array
     */
    protected function defineIntegrations()
    {
        $list = ['All' => true, 'Intercom' => false, 'SnapEngage' => false, 'wootric' => false];

        if (\XLite\Core\Auth::getInstance()->hasRootAccess()) {
            $list['SnapEngage'] = true;
            $list['wootric'] = true;
        }

        return $list;
    }

    /**
     * @param AMessage[] $messages
     *
     * @return array
     */
    protected function prepareMessages($messages)
    {
        return array_map(function ($message) {
            return $message instanceof AMessage ? $message->toArray() : $message;
        }, $messages);
    }

    /**
     * Check - debug mode or not
     *
     * @return boolean
     */
    protected function isDebug()
    {
        return false;
    }

    /**
     * Get settings
     *
     * @return array
     */
    protected function getSettings()
    {
        $adminProfile = \XLite\Core\Auth::getInstance()->getProfile();

        return [
            'writeKey'    => $this->getMediator()->getWriteKey(),
            'messages'    => $this->getMessages(),
            'ready'       => true,
            'context'     => $this->getMediator()->getOptions(),
            'admin_email' => $adminProfile ? $adminProfile->getLogin() : '',
            'email'       => \XLite\Core\Config::getInstance()->XC->Concierge->user_id,
        ];
    }

    protected function getIntercomHash()
    {
        $userId = \XLite\Core\Config::getInstance()->XC->Concierge->user_id;

        return ExecuteCached::executeCached(
            static function () use ($userId) {
                $hash = '';
                if ($userId) {
                    $request = new Request(self::INTERCOM_HASH_URL);
                    $request->verb = 'POST';
                    $request->body = http_build_query(['user_id' => $userId], null, '&');
                    $result = $request->sendRequest();

                    $hash = $result && $result->body ? $result->body : $hash;
                }

                return trim($hash);
            },
            [self::class, 'getIntercomHash', $userId]
        );
    }
}
