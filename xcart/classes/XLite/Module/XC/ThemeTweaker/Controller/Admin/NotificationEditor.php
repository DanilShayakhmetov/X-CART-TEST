<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\Controller\Admin;

use XLite\Core\Database;
use XLite\Core\Request;
use XLite\Module\XC\ThemeTweaker\Core\Notifications\Data;
use XLite\Module\XC\ThemeTweaker\Core\Notifications\ErrorTranslator;

/**
 * ThemeTweaker controller
 */
class NotificationEditor extends \XLite\Controller\Admin\AAdmin
{
    protected $dataSource;
    protected $failedTemplates = [];

    public function __construct(array $params)
    {
        parent::__construct($params);

        $this->params = array_merge($this->params, ['templatesDirectory', 'interface']);
    }

    public function handleRequest()
    {
        parent::handleRequest();

        if (!$this->getAction() && !$this->isInterfaceValid()) {
            $notification = $this->getNotification();
            $interface = $notification->getAvailableForCustomer() || $notification->getEnabledForCustomer()
                ? \XLite::CUSTOMER_INTERFACE
                : \XLite::ADMIN_INTERFACE;

            $this->redirect($this->buildURL('notification_editor', '', [
                'templatesDirectory' => $this->getNotification()->getTemplatesDirectory(),
                'interface'          => $interface,
            ]));
        }
    }

    /**
     * @return bool
     */
    protected function isInterfaceValid()
    {
        $notification = $this->getNotification();
        $interface = Request::getInstance()->interface;

        return $interface && (
                $interface === \XLite::ADMIN_INTERFACE
                && (
                    $notification->getAvailableForAdmin()
                    || $notification->getEnabledForAdmin()
                )
                || $interface === \XLite::CUSTOMER_INTERFACE
                && (
                    $notification->getAvailableForCustomer()
                    || $notification->getEnabledForCustomer()
                )
            );
    }

    public function isVisible()
    {
        return parent::isVisible()
            && $this->getNotification()
            && $this->getDataSource()->isEditable()
            && $this->getDataSource()->isAvailable();
    }

    protected function isDisplayHtmlTree()
    {
        return false;
    }

    protected function doNoAction()
    {
        parent::doNoAction();

        if (!$this->getDataSource()->isSuitable()) {
            foreach ($this->getDataSource()->getSuitabilityErrors() as $provider => $errors) {
                foreach ($errors as $error) {
                    $translation = ErrorTranslator::translateSuitabilityError(
                        $provider,
                        isset($error['code']) ? $error['code'] : null,
                        isset($error['value']) ? $error['value'] : null
                    );

                    if ($translation) {
                        switch (isset($error['type']) ? $error['type'] : null) {
                            case 'warning':
                                \XLite\Core\TopMessage::addWarning($translation);
                                break;
                            case 'info':
                                \XLite\Core\TopMessage::addInfo($translation);
                                break;
                            default:
                                \XLite\Core\TopMessage::addError($translation);
                        }
                    }
                }
            }
        }
    }

    /**
     * @return \XLite\Model\Notification|null
     */
    public function getNotification()
    {
        return Database::getRepo('XLite\Model\Notification')->find(
            Request::getInstance()->templatesDirectory
        );
    }

    protected function doActionChangeData()
    {
        $data = Request::getInstance()->data ?: [];

        foreach ($this->getDataSource()->update($data) as $provider => $errors) {
            foreach ($errors as $error) {
                $translation = ErrorTranslator::translateError(
                    $provider,
                    isset($error['code']) ? $error['code'] : null,
                    isset($error['value']) ? $error['value'] : null
                );

                if ($translation) {
                    \XLite\Core\TopMessage::addWarning($translation);
                }
            }
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

    /**
     * Mark later
     *
     * @param string $template
     */
    public function addFailedTemplate($template)
    {
        $this->failedTemplates = array_unique(
            array_merge($this->failedTemplates, [
                $template
            ])
        );
    }

    /**
     * @return array
     */
    public function getFailedTemplates()
    {
        return $this->failedTemplates;
    }

    /**
     * @return bool
     */
    public function isTemplateFailed()
    {
        return !!$this->failedTemplates;
    }
}
