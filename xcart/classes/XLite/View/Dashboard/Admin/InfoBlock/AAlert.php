<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Dashboard\Admin\InfoBlock;

abstract class AAlert extends \XLite\View\AView
{
    /**
     * @return array
     */
    public function getCSSFiles()
    {
        $result   = parent::getCSSFiles();
        $result[] = 'dashboard/info_block/alert/style.less';

        return $result;
    }

    /**
     * @return int
     */
    abstract protected function getCounter();

    /**
     * @return string
     */
    abstract protected function getHeader();

    /**
     * @return string
     */
    protected function getHeaderUrl()
    {
        return '';
    }

    /**
     * @return string
     */
    protected function getIcon()
    {
        return $this->getSVGImage('images/info.svg');
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
        return 'infoblock-alert';
    }

    /**
     * @return array
     */
    protected function getTagAttributes()
    {
        return [
            'class' => $this->getClass(),
        ];
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'dashboard/info_block/alert/body.twig';
    }

    /**
     * @return bool
     */
    protected function isVisible()
    {
        return parent::isVisible() && $this->getCounter();
    }
}
