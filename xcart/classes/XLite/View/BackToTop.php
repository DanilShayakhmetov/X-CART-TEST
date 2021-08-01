<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * Class BackToTop
 *
 * @ListChild (list="center")
 */
class BackToTop extends \XLite\View\AView
{
    /**
     * @inheritDoc
     */
    protected function getDefaultTemplate()
    {
        return 'back_to_top/body.twig';
    }

    /**
     * @inheritDoc
     */
    public function getCSSFiles()
    {
        return array_merge(
            parent::getCSSFiles(),
            [
                'back_to_top/style.less'
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function getJSFiles()
    {
        return array_merge(
            parent::getJSFiles(),
            [
                'back_to_top/component.js'
            ]
        );
    }


}