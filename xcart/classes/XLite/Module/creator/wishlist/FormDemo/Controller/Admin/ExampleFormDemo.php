<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XCExample\FormDemo\Controller\Admin;

/**
 * ExampleFormDemo
 */
class ExampleFormDemo extends \XLite\Controller\Admin\AAdmin
{     
    /**
     * Get target title
     */
    public function getTitle()
    {
        return static::t('Example Form Demo');
    }

    public function doActionSend()
    {
        $message = \XLite\Core\Request::getInstance()->message;

        if ($message) {
            \XLite\Core\TopMessage::getInstance()->add($message);
        }

        $this->setReturnURL(
            $this->buildURL('example_form_demo', '', array())
        );
    }
}