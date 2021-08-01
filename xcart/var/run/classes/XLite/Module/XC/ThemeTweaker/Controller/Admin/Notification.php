<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\Controller\Admin;

use XLite\Core\Auth;
use XLite\Core\Mail\Sender;
use XLite\Core\Mailer;
use XLite\Core\Request;
use XLite\Core\TopMessage;
use XLite\Module\XC\ThemeTweaker\Core\Notifications\Data;
use XLite\Module\XC\ThemeTweaker\Core\Notifications\DataPreProcessor;

/**
 * Notification
 */
 class Notification extends \XLite\Controller\Admin\NotificationAbstract implements \XLite\Base\IDecorator
{
    protected function doActionSendTestEmail()
    {
        $request = Request::getInstance();
        $dataSource = $this->getDataSource();
        $templatesDirectory = $request->templatesDirectory;
        $interface = $request->page === \XLite::ADMIN_INTERFACE
            ? \XLite::ADMIN_INTERFACE
            : \XLite::CUSTOMER_INTERFACE;

        if (
            $dataSource->isEditable()
            && $dataSource->isAvailable()
        ) {
            $to = Auth::getInstance()->getProfile()->getLogin();

            $result = Mailer::getInstance()->sendNotificationPreview(
                $templatesDirectory,
                $to,
                $interface,
                DataPreProcessor::prepareDataForNotification(
                    $templatesDirectory,
                    $dataSource->getData()
                )
            );

            if ($result) {
                TopMessage::addInfo('The test email notification has been sent to X', ['email' => $to]);
            } else {
                TopMessage::addWarning('Failure sending test email to X', ['email' => $to]);
            }
        }

        $this->setReturnURL($this->buildFullURL(
            $request->from_notification ? 'notification' : 'notification_editor',
            '',
            [
                'templatesDirectory'                               => $request->templatesDirectory,
                $request->from_notification ? 'page' : 'interface' => $interface,
            ]
        ));
    }

    /**
     * Process request
     */
    public function processRequest()
    {
        $request = Request::getInstance();
        $dataSource = $this->getDataSource();

        if (
            $request->preview
            && $dataSource->isEditable()
            && $dataSource->isAvailable()
        ) {
            $innerInterface = Request::getInstance()->page === \XLite::ADMIN_INTERFACE
                ? \XLite::ADMIN_INTERFACE
                : \XLite::CUSTOMER_INTERFACE;

            echo Sender::getNotificationPreviewContent(
                $request->templatesDirectory,
                DataPreProcessor::prepareDataForNotification(
                    $request->templatesDirectory,
                    $dataSource->getData()
                ),
                $innerInterface
            );

        } else {
            parent::processRequest();
        }
    }

    /**
     * @return Data
     */
    public function getDataSource()
    {
        return $this->dataSource
            ?: ($this->dataSource = new Data($this->getNotification()));
    }
}
