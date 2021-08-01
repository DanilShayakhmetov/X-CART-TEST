<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Onboarding\Core;

use XLite\Core\Cache\ExecuteCached;
use XLite\Module\XC\Onboarding\Controller\Admin\OnboardingWizard;
use XLite\Module\XC\Onboarding\View\WizardStep;

/**
 * WizardState core class
 */
class WizardState extends \XLite\Base\Singleton
{
    const COOKIE_CURRENT_PROGRESS = 'Wizard_currentProgress';
    const COOKIE_MAX_PROGRESS     = 'Wizard_maxProgress';
    const COOKIE_LANDMARKS        = 'Wizard_landmarks';
    const COOKIE_LASTLOGO         = 'Wizard_lastLogo';

    /**
     * Returns current wizard step
     */
    public function getWizardStep()
    {
        return $this->getCurrentStep();
    }

    public function getCurrentStep()
    {
        return \XLite\Core\Request::getInstance()->{self::COOKIE_CURRENT_PROGRESS} ?: 'intro';
    }

    public function setCurrentStep($stepName)
    {
        $steps = $this->defineWizardSteps();

        if (isset($steps[$stepName])) {
            $maxStep = $this->getMaxAchievedStep();
            if (
                !isset($steps[$maxStep])
                || $steps[$maxStep]['progress'] <= $steps[$stepName]['progress']
            ) {
                \XLite\Core\Request::getInstance()->setJsCookie(self::COOKIE_MAX_PROGRESS, $stepName);
            }

            return \XLite\Core\Request::getInstance()->setJsCookie(self::COOKIE_CURRENT_PROGRESS, $stepName);
        }

        return false;
    }

    public function setAddedProduct()
    {
        \XLite\Core\Request::getInstance()->setJsCookie(self::COOKIE_LANDMARKS, '["product"]');
    }

    public function getMaxAchievedStep()
    {
        return \XLite\Core\Request::getInstance()->{self::COOKIE_MAX_PROGRESS} ?: $this->getCurrentStep();
    }

    public function getWizardProgress()
    {
        $step = $this->getMaxAchievedStep();

        $steps = $this->defineWizardSteps();

        if (isset($steps[$step])) {
            return $steps[$step]['progress'];
        }

        return 0;
    }

    /**
     * @return string|null
     */
    public function getNextStep()
    {
        $nextStep = null;

        $nextIterator = new \ArrayIterator($this->defineWizardSteps());
        while ($nextIterator->valid()) {
            if ($nextIterator->key() === $this->getCurrentStep()) {
                $nextIterator->next();
                $nextStep = $nextIterator->key();
                break;
            }
            $nextIterator->next();
        }

        return $nextStep;
    }

    /**
     * Defines all available wizard steps
     *
     * @return array
     */
    public function defineWizardSteps()
    {
        return OnboardingWizard::isCloud()
            ? $this->getCloudWizardSteps()
            : $this->getDefaultWizardSteps();
    }

    protected function getDefaultWizardSteps(): array
    {
        return [
            'intro'              => [
                'progress' => 0,
                'name'     => 'Intro',
                'body'     => WizardStep\Intro::class,
            ],
            'add_product'        => [
                'progress' => 10,
                'name'     => 'Product',
                'body'     => WizardStep\AddProduct::class,
            ],
            'product_added'      => [
                'progress' => 20,
                'name'     => 'Product added',
                'body'     => WizardStep\ProductAdded::class,
            ],
            'company_logo'       => [
                'progress' => 30,
                'name'     => 'Logo upload',
                'body'     => WizardStep\CompanyLogo::class,
            ],
            'company_logo_added' => [
                'progress' => 35,
                'name'     => 'Logo confirmation',
                'body'     => WizardStep\CompanyLogoAdded::class,
            ],
            'location'           => [
                'progress' => 50,
                'name'     => 'Location',
                'body'     => WizardStep\Location::class,
            ],
            'company_info'       => [
                'progress' => 55,
                'name'     => 'Company info',
                'body'     => WizardStep\CompanyInfo::class,
            ],
            'shipping'           => [
                'progress' => 70,
                'name'     => 'Shipping type',
                'body'     => WizardStep\Shipping::class,
            ],
            'shipping_rates'     => [
                'progress' => 75,
                'name'     => 'Shipping method/rate',
                'body'     => WizardStep\ShippingRates::class,
            ],
            'shipping_done'      => [
                'progress' => 80,
                'name'     => 'Shipping success',
                'body'     => WizardStep\ShippingDone::class,
            ],
            'payment'            => [
                'progress' => 90,
                'name'     => 'Payment',
                'body'     => WizardStep\Payment::class,
            ],
            'done'               => [
                'progress' => 100,
                'name'     => 'Wizard completed',
                'body'     => WizardStep\Done::class,
            ],
        ];
    }

    protected function getCloudWizardSteps(): array
    {
        return [
            'intro'               => [
                'progress' => 0,
                'name'     => 'Intro',
                'body'     => WizardStep\Intro::class,
            ],
            'business_info'       => [
                'progress' => 10,
                'name'     => 'Tell us about your business',
                'body'     => WizardStep\BusinessInfo::class,
            ],
            'add_product_cloud'   => [
                'progress' => 50,
                'name'     => 'Product',
                'body'     => WizardStep\AddProductCloud::class,
            ],
            'product_added_cloud' => [
                'progress' => 70,
                'name'     => 'Product added',
                'body'     => WizardStep\ProductAddedCloud::class,
            ],
            'company_logo_cloud'  => [
                'progress' => 100,
                'name'     => 'Logo upload',
                'body'     => WizardStep\CompanyLogoCloud::class,
            ],
        ];
    }

    public function getDemoEntityTypes()
    {
        return [
            'XLite\Model\Order',
            'XLite\Model\Product',
            'XLite\Model\Category',
        ];
    }

    /**
     * Checks if store contains demo catalog
     * @return boolean
     */
    public function hasDemoCatalog()
    {
        return ExecuteCached::executeCachedRuntime(function () {
            $types = $this->getDemoEntityTypes();

            foreach ($types as $type) {
                $repo = \XLite\Core\Database::getRepo($type);

                if (method_exists($repo, 'getDemoEntitiesCount')) {
                    $count = $repo->getDemoEntitiesCount();

                    if ($count > 0) {
                        return true;
                    }
                }
            }

            return false;
        }, ['OnboardingWizard::hasDemoCatalog']);
    }

    public function deleteDemoCatalog()
    {
        $types = $this->getDemoEntityTypes();

        foreach ($types as $type) {
            $repo = \XLite\Core\Database::getRepo($type);

            if (method_exists($repo, 'deleteDemoEntities')) {
                $repo->deleteDemoEntities();
            }
        }
    }

    public function getConfigOption($key)
    {
        return \XLite\Core\Config::getInstance()->XC->Onboarding->{$key};
    }

    public function updateConfigOption($key, $value)
    {
        \XLite\Core\Database::getRepo('\XLite\Model\Config')->createOption([
            'category' => 'XC\Onboarding',
            'name'     => $key,
            'value'    => $value,
        ]);
    }

    public function getLastAddedProductId()
    {
        return \XLite\Core\Session::getInstance()->onboardingLastAddedProductId;
    }

    public function setLastAddedProductId($id)
    {
        \XLite\Core\Session::getInstance()->onboardingLastAddedProductId = $id;
    }

    public function reset()
    {
        $request = \XLite\Core\Request::getInstance();

        $this->setLastAddedProductId(null);
        $request->setCookie(self::COOKIE_CURRENT_PROGRESS, null);
        $request->setCookie(self::COOKIE_MAX_PROGRESS, null);
        $request->setCookie(self::COOKIE_LASTLOGO, null);
        $request->setCookie(self::COOKIE_LANDMARKS, null);
        $this->updateConfigOption('wizard_state', 'visible');
        $this->updateConfigOption('wizard_force_disabled', false);
        $this->setLastAddedProductId(null);
    }
}
