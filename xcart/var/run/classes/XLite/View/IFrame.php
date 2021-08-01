<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * IFrame
 */
abstract class IFrame extends \XLite\View\AView
{
    public function getIFrameAttributes()
    {
        return [
            'width' => '100%',
            'height' => '100%',
            'src' => $this->getSrc(),
        ];
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'iframe.twig';
    }

    /**
     * @return string
     */
    abstract protected function getSrc();
}
