<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\View\Settings;

use \XLite\Module\XC\MailChimp\Core;

/**
 * Tabs
 */
class MailChimpAPISettings extends \XLite\Module\XC\MailChimp\View\Settings\ASettings implements \XLite\Core\PreloadedLabels\ProviderInterface
{
    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . '/settings.twig';
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $return = parent::getCSSFiles();

        $return[] = $this->getDir() . '/settings.css';

        return $return;
    }

    /**
     * @inheritdoc
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'modules/XC/MailChimp/settings/script.js';

        return $list;
    }

    /**
     * @inheritdoc
     */
    public function getPreloadedLanguageLabels()
    {
        return [
            'e-Commerce Analytics disable warning' => static::t('e-Commerce Analytics disable warning')
        ];
    }

    /**
     * Get current sections
     *
     * @return array
     */
    protected function getSections()
    {
        return array(Core\MailChimpSettings::SECTION_MAILCHIMP_API);
    }
}
