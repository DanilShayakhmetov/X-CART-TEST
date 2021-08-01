<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Onboarding\View;

use XLite\Module\XC\Onboarding\Controller\Admin\OnboardingWizard;

/**
 * File uploader
 */
 class FileUploader extends \XLite\View\FileUploaderAbstract implements \XLite\Base\IDecorator
{
    /**
     * Get a list of JS files required to display the widget properly
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        if (\XLite::getController() instanceof OnboardingWizard) {
            if (OnboardingWizard::isCloud()) {
                $list[] = 'modules/XC/Onboarding/file_uploader/onboarding_cloud.js';
            } else {
                $list[] = 'modules/XC/Onboarding/file_uploader/onboarding.js';
            }
        }

        return $list;
    }

    /**
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        if (\XLite::getController() instanceof OnboardingWizard && OnboardingWizard::isCloud()) {
            $list[] = 'modules/XC/Onboarding/file_uploader/onboarding_cloud.less';
        }

        return $list;
    }

}
