<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XCExample\FormDemo\View\Page\Admin;

/**
 * ExampleFormDemoPage
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class ExampleFormDemo extends \XLite\View\AView
{
        
    /**
     * Return list of allowed targets
     */
    public static function getAllowedTargets()
    {
        return array_merge(parent::getAllowedTargets(), array('example_form_demo'));
    }
      
    /**
     * Return widget default template
     */
    public function getDefaultTemplate()
    {
        return 'modules/XCExample/FormDemo/page/example_form_demo/body.twig';
    }
}
