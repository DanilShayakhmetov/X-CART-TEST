<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Form;


class ApplyAttributeValues extends \XLite\View\Form\AForm
{
    /**
     * getDefaultTarget
     *
     * @return string
     */
    protected function getDefaultTarget()
    {
        return 'product';
    }

    /**
     * getDefaultAction
     *
     * @return string
     */
    protected function getDefaultAction()
    {
        return 'cancel_applying_attr_values';
    }

    /**
     * Return list of additional params
     *
     * @return array
     */
    protected function getFormParams()
    {
        $params = parent::getFormParams();

        $request = \XLite\Core\Request::getInstance();

        $params['product_id'] = $request->product_id;
        $params['page'] = $request->page;

        return $params;
    }
}