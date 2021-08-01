<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Dashboard\Admin\InfoBlock;

abstract class ANotification extends \XLite\View\AView
{
    /**
     * @return array
     */
    public function getCSSFiles()
    {
        $result   = parent::getCSSFiles();
        $result[] = 'dashboard/info_block/notification/style.less';

        return $result;
    }

    /**
     * @return array
     */
    public function getJSFiles()
    {
        $result   = parent::getJSFiles();
        $result[] = 'dashboard/info_block/notification/controller.js';

        return $result;
    }

    /**
     * @return string
     */
    abstract protected function getNotificationType();

    /**
     * @return string
     */
    abstract protected function getHeader();

    /**
     * @return int
     */
    protected function getCounter()
    {
        return 0;
    }

    /**
     * @return string
     */
    protected function getHeaderUrl()
    {
        return '';
    }

    /**
     * @return bool
     */
    protected function isExternal()
    {
        return false;
    }

    /**
     * @return string
     */
    protected function getClass()
    {
        return 'infoblock-notification';
    }

    /**
     * @return array
     */
    protected function getTagAttributes()
    {
        return [
            'class'                  => $this->getClass(),
            'data-notification-type' => $this->getNotificationType(),
        ];
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'dashboard/info_block/notification/body.twig';
    }

    /**
     * @return bool
     */
    protected function isVisible()
    {
        return parent::isVisible() && $this->getLastReadTimestamp() < $this->getLastUpdateTimestamp();
    }

    /**
     * Last read timestamp
     *
     * @return integer
     */
    protected function getLastReadTimestamp()
    {
        return (int) (\XLite\Core\Request::getInstance()->{$this->getNotificationType() . 'ReadTimestamp'} ?? 0);
    }

    /**
     * Return update timestamp
     *
     * @return integer
     */
    protected function getLastUpdateTimestamp()
    {
        $result = \XLite\Core\TmpVars::getInstance()->{$this->getNotificationType() . 'UpdateTimestamp'};

        if (null === $result) {
            $result = LC_START_TIME;
            $this->setLastUpdateTimestamp($result);
        }

        return $result;
    }

    /**
     * Set update timestamp
     *
     * @param integer $timestamp Timestamp
     *
     * @return void
     */
    protected function setLastUpdateTimestamp($timestamp)
    {
        \XLite\Core\TmpVars::getInstance()->{$this->getNotificationType() . 'UpdateTimestamp'} = $timestamp;
    }
}
