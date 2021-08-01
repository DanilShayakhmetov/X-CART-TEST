<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Customer;

/**
 * User profile page controller
 */
class ForceChangePassword extends \XLite\Controller\Customer\Profile
{
    /**
     * Return class name of the register form
     *
     * @return string|void
     */
    protected function getModelFormClass()
    {
        return '\XLite\View\Model\Profile\ForceChangePassword';
    }

    protected function doActionModify()
    {
        parent::doActionModify();

        if ($this->getModelForm()->isValid()) {
            $this->setHardRedirect(true);
        }
    }
}
