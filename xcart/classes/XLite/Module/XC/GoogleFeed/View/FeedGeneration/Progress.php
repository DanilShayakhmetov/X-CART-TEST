<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\GoogleFeed\View\FeedGeneration;

use XLite\Module\XC\GoogleFeed\Logic\Feed\Generator;
use XLite\View\EventTaskProgressProviderTrait;

/**
 * Google Feed generation Progress
 */
class Progress extends \XLite\View\AView
{
    use EventTaskProgressProviderTrait;

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/XC/GoogleFeed/admin/progress_style.css';

        return $list;
    }

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'modules/XC/GoogleFeed/admin/progress_controller.js';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/XC/GoogleFeed/admin/progress.twig';
    }

    /**
     * Returns processing unit
     *
     * @return mixed
     */
    protected function getProcessor()
    {
        return Generator::getInstance();
    }
}