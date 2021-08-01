<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField;

use Includes\Utils\Module\Manager;

class ContactUsComment extends \XLite\View\FormField\AFormField
{
    /**
     * Return field type
     *
     * @return string
     */
    public function getFieldType()
    {
        return self::FIELD_TYPE_LABEL;
    }

    /**
     * Return field template
     *
     * @return string
     */
    protected function getFieldTemplate()
    {
        return '';
    }

    /**
     * @return string
     */
    public function getLink()
    {
        if (Manager::getRegistry()->getModule('CDev\ContactUs')) {
            return Manager::getRegistry()->isModuleEnabled('CDev\ContactUs')
                ? Manager::getRegistry()->getModuleSettingsUrl('CDev\ContactUs')
                : Manager::getRegistry()->getModuleServiceURL('CDev\ContactUs');
        }

        return null;
    }

    /**
     * @return bool
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && $this->getLink();
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'form_field/contactUsComment.twig';
    }
}
