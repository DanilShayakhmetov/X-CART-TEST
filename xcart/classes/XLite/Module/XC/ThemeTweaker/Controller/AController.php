<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\Controller;

use XLite\Module\XC\ThemeTweaker\Core\ThemeTweaker;

/**
 * Payment method
 */
abstract class AController extends \XLite\Controller\AController implements \XLite\Base\IDecorator
{
    /**
     * Process request
     *
     * @return void
     */
    public function processRequest()
    {
        parent::processRequest();

        if (
            !$this->suppressOutput
            && !$this->isAJAX()
            && ThemeTweaker::getInstance()->isInWebmasterMode()
            && $this->isDisplayHtmlTree()
        ) {
            $viewer = $this->getViewer();

            echo $viewer::getHtmlTree();
        }
    }

    protected function isDisplayHtmlTree()
    {
        return true;
    }

    /**
     * Retrieve AJAX output content from viewer
     *
     * @param mixed $viewer Viewer to display in AJAX
     *
     * @return string
     */
    protected function getAJAXOutputContent($viewer)
    {
        return parent::getAJAXOutputContent($viewer) . $viewer::getHtmlTree();
    }
}
