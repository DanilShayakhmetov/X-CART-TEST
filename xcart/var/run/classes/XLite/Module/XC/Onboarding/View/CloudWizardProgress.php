<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Onboarding\View;

class CloudWizardProgress extends WizardProgress
{
    /**
     * @return string
     */
    protected function getFirstIndex(): string
    {
        return 'business';
    }

    /**
     * @return string
     */
    protected function getDivStyle()
    {
        return 'onboarding-wizard-progress onboarding-wizard-progress-cloud';
    }

    /**
     * @return array[]
     */
    protected function getLandmarks()
    {
        return [
            'business' => [
                'index'    => 'business',
                'name'     => 'Tell us about your business',
                'target'   => 'business_info',
                'steps'    => ['business_info'],
                'activeOn' => 10,
                'image'    => 'modules/XC/Onboarding/images/step-business.svg',
            ],
            'product'  => [
                'index'    => 'product',
                'name'     => 'Add your first product',
                'target'   => 'add_product_cloud',
                'steps'    => ['add_product_cloud'],
                'activeOn' => 50,
                'image'    => 'modules/XC/Onboarding/images/step-product.svg',
            ],
            'company'  => [
                'index'    => 'company',
                'name'     => 'Upload company logo',
                'target'   => 'company_logo_cloud',
                'steps'    => ['company_logo_cloud'],
                'activeOn' => 100,
                'image'    => 'modules/XC/Onboarding/images/step-company.svg',
            ],
        ];
    }
}