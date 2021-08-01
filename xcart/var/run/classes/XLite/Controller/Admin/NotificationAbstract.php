<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

use XLite\Core\Request;

/**
 * Notification controller
 */
abstract class NotificationAbstract extends \XLite\Controller\Admin\AAdmin
{
    use \XLite\Controller\Features\FormModelControllerTrait;

    public function __construct(array $params)
    {
        parent::__construct($params);

        $this->params = array_merge($this->params, ['page', 'templatesDirectory']);
    }

    public function handleRequest()
    {
        $this->handleNotificationPageParam();

        parent::handleRequest();
    }

    /**
     * Redirect if url invalid
     */
    protected function handleNotificationPageParam()
    {
        $page = Request::getInstance()->page;

        if (!$this->isValidPage($page)) {
            if ($url = $this->buildValidPageUrl()) {
                $this->redirect($this->buildValidPageUrl());
            } else {
                $this->redirect($this->buildURL('notifications'));
            }
        }
    }

    protected function addBaseLocation()
    {
        parent::addBaseLocation();

        $this->addLocationNode(static::t('Email notifications'), $this->buildURL('notifications'));
    }

    /**
     * @param $page
     *
     * @return bool
     */
    protected function isValidPage($page)
    {
        $notification = $this->getNotification();

        return in_array($page, [
                'admin',
                'customer',
            ], true)
            && (
                $page !== 'customer'
                || $notification->getEnabledForCustomer()
                || $notification->getAvailableForCustomer()
            )
            && (
                $page !== 'admin'
                || $notification->getEnabledForAdmin()
                || $notification->getAvailableForAdmin()
            );
    }

    /**
     * @return string
     */
    protected function buildValidPageUrl()
    {
        $notification = $this->getNotification();

        if ($notification->getAvailableForCustomer() || $notification->getEnabledForCustomer()) {
            return $this->buildURL('notification', '', [
                'templatesDirectory' => $notification->getTemplatesDirectory(),
                'page'               => 'customer',
            ]);
        }

        if ($notification->getAvailableForAdmin() || $notification->getEnabledForAdmin()) {
            return $this->buildURL('notification', '', [
                'templatesDirectory' => $notification->getTemplatesDirectory(),
                'page'               => 'admin',
            ]);
        }

        return '';
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        $notification = $this->getNotification();

        return $notification
            ? $notification->getName()
            : '';
    }

    /**
     * Returns description of current notification
     *
     * @return string
     */
    public function getDescription()
    {
        $notification = $this->getNotification();

        return $notification
            ? $notification->getDescription()
            : '';
    }

    /**
     * Returns object to get initial data and populate submitted data to
     *
     * @return \XLite\Model\DTO\Base\ADTO
     */
    public function getFormModelObject()
    {
        $class = $this->getFormModelObjectClass();

        return new $class($this->getNotification());
    }

    /**
     * @return \XLite\Model\DTO\Base\ADTO
     */
    protected function getFormModelObjectClass()
    {
        return 'admin' === Request::getInstance()->page
            ? 'XLite\Model\DTO\Settings\Notification\Admin'
            : 'XLite\Model\DTO\Settings\Notification\Customer';
    }

    /**
     * Update model
     *
     * @return void
     */
    protected function doActionUpdate()
    {
        $dto = $this->getFormModelObject();
        $formModel = new \XLite\View\FormModel\Settings\Notification\Notification(['object' => $dto]);

        $form = $formModel->getForm();
        $data = \XLite\Core\Request::getInstance()->getData();
        $rawData = \XLite\Core\Request::getInstance()->getNonFilteredData();

        $form->submit($data[$this->formName]);

        if ($form->isValid()) {
            $dto->populateTo($this->getNotification(), $rawData[$this->formName]);
            \XLite\Core\Database::getEM()->flush();

            \XLite\Core\TopMessage::addInfo('The notification has been updated');

        } else {
            $this->saveFormModelTmpData($rawData[$this->formName]);
        }
    }

    /**
     * Returns notification
     *
     * @return \XLite\Model\Notification
     */
    public function getNotification()
    {
        $id = \XLite\Core\Request::getInstance()->templatesDirectory;

        return $id
            ? \XLite\Core\Database::getRepo('XLite\Model\Notification')->find($id)
            : null;
    }

    /**
     * Check controller visibility
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible() && $this->getNotification();
    }

    /**
     * @return string
     */
    public function getPage()
    {
        return $this->page;
    }
}
