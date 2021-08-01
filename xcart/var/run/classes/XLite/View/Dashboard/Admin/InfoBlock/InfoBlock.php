<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Dashboard\Admin\InfoBlock;

class InfoBlock extends \XLite\View\AView
{
    /**
     * @return array
     */
    public function getCSSFiles()
    {
        $result   = parent::getCSSFiles();
        $result[] = 'dashboard/info_block/style.less';

        return $result;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'dashboard/info_block/body.twig';
    }

    /**
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible() && \XLite\Core\Auth::getInstance()->isAdmin();
    }
}
