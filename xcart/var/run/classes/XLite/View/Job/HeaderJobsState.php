<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Job;

/**
 * Class HeaderJobsState
 * @ListChild (list="admin.main.page.header.right", zone="admin", weight="90")
 */
class HeaderJobsState extends \XLite\View\AView
{
    protected function isVisible()
    {
        //TODO remove when job with steps will be ready
        return false;
    }

    /**
     * @inheritdoc
     */
    public static function getDisallowedTargets()
    {
        return [
            'login'
        ];
    }

    /**
     * @return string
     */
    public function getDir()
    {
        return 'job/header_state_bar';
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . '/body.twig';
    }

    /**
     * @inheritDoc
     */
    public function getJSFiles()
    {
        return array_merge(
            parent::getJSFiles(),
            [
                $this->getDir() . '/progressLoadable.js',
                $this->getDir() . '/controller.js',
                'job/progressService.js',
            ]
        );
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = $this->getDir() . '/style.less';
        $list[] = 'job/style.less';

        return $list;
    }

}
