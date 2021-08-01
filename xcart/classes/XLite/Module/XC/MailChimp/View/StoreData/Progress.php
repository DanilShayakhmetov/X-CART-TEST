<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\View\StoreData;

/**
 * Class Progress
 */
class Progress extends \XLite\View\AView
{
    use \XLite\View\EventTaskProgressProviderTrait;

    /**
     * @inheritDoc
     */
    public function getJSFiles()
    {
        return array_merge(
            parent::getJSFiles(),
            [
                'modules/XC/MailChimp/store_data/progress/controller.js'
            ]
        );
    }

    /**
     * Returns processor instance
     *
     * @return mixed
     */
    protected function getProcessor()
    {
        return \XLite\Module\XC\MailChimp\Logic\UploadingData\Generator::getInstance();
    }

    /**
     * @inheritDoc
     */
    public static function getAllowedTargets()
    {
        return array_merge(
            parent::getAllowedTargets(),
            [
                'mailchimp_store_data'
            ]
        );
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/XC/MailChimp/store_data/progress/body.twig';
    }
}