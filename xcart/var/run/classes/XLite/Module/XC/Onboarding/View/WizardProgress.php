<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Onboarding\View;

use XLite\Module\XC\Onboarding\Core\WizardState;

/**
 * Wizard progress
 */
class WizardProgress extends \XLite\View\AView
{
    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/XC/Onboarding/wizard_progress';
    }

    /**
     * @return array
     */
    public function getJSFiles()
    {
        return [
            $this->getDir() . '/progress.js',
        ];
    }

    /**
     * @return array
     */
    public function getCSSFiles()
    {
        return [
            $this->getDir() . '/progress.css',
        ];
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . '/progress.twig';
    }

    /**
     * @return int|mixed
     */
    protected function getPercentage()
    {
        return WizardState::getInstance()->getWizardProgress();
    }

    /**
     * @return string
     */
    protected function getFirstIndex(): string
    {
        return 'product';
    }

    /**
     * @return string
     */
    protected function getDivStyle()
    {
        return 'onboarding-wizard-progress';
    }

    /**
     * @return array[]
     */
    protected function getLandmarks()
    {
        return [
            'product'  => [
                'index'    => 'product',
                'name'     => 'Add first product',
                'target'   => 'add_product',
                'steps'    => ['add_product'],
                'activeOn' => 10,
                'image'    => 'modules/XC/Onboarding/images/step-product.svg',
            ],
            'company'  => [
                'index'    => 'company',
                'name'     => 'Upload company logo',
                'target'   => 'company_logo',
                'steps'    => ['company_logo', 'company_logo_added'],
                'activeOn' => 30,
                'image'    => 'modules/XC/Onboarding/images/step-company.svg',
            ],
            'location' => [
                'index'    => 'location',
                'name'     => 'Verify geo settings',
                'target'   => 'location',
                'steps'    => ['location', 'company_info'],
                'activeOn' => 50,
                'image'    => 'modules/XC/Onboarding/images/step-location.svg',
            ],
            'shipping' => [
                'index'    => 'shipping',
                'name'     => 'Set up shipping methods',
                'target'   => 'shipping',
                'steps'    => ['shipping', 'shipping_rates'],
                'activeOn' => 70,
                'image'    => 'modules/XC/Onboarding/images/step-shipping.svg',
            ],
            'payment'  => [
                'index'    => 'payment',
                'name'     => 'Set up payment gateways',
                'target'   => 'payment',
                'steps'    => ['payment'],
                'activeOn' => 90,
                'image'    => 'modules/XC/Onboarding/images/step-payment.svg',
            ],
        ];
    }
}