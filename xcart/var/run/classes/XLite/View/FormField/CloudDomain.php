<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField;

class CloudDomain extends \XLite\View\FormField\Label
{
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $changeDomainLink = '<a href="' . $this->buildURL('cloud_domain_name') . '">'
            . static::t('Change domain name')
            . '</a>';

        $this->widgetParams[static::PARAM_COMMENT]->setValue($changeDomainLink);
    }

    public function getLabel()
    {
        return static::t('Domain name');
    }

    protected function getLabelValue()
    {
        return \XLite::getInstance()->getOptions(['host_details', 'http_host_orig'])
            ?: \XLite::getInstance()->getOptions(['host_details', 'http_host']);
    }
    
    protected function isVisible()
    {
        return parent::isVisible()
            && \XLite::getInstance()->getOptions(['service', 'is_cloud'])
            && !\XLite::getInstance()->getOptions(['service', 'is_trial']);
    }
}
