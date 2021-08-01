<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Onboarding\Controller\Admin;

use Symfony\Component\Form\FormInterface;
use XLite\Core\Config;
use XLite\Model\Product;
use XLite\Module\XC\Onboarding\Core\WizardState;
use XLite\Module\XC\Onboarding\Main;
use XLite\Module\XC\Onboarding\View\FormModel\Product\Simplified as FormModelSimplified;
use XLite\Module\XC\Onboarding\Model\DTO\Product\Simplified as DTOSimplified;
use XLite\Module\XC\Onboarding\View\WizardStep\ProductAdded as ProductAddedView;

/**
 * FacebookMarketing
 */
abstract class OnboardingWizardAbstract extends \XLite\Controller\Admin\AAdmin
{
    use \XLite\Controller\Features\FormModelControllerTrait;

    const SHIPPING_METHOD_CODE = 'ONBOARDING_METHOD';

    /**
     * Define the actions with no secure token
     *
     * @return array
     */
    public static function defineFreeFormIdActions()
    {
        return array_merge(
            parent::defineFreeFormIdActions(),
            array(
                'reset',
            )
        );
    }

    /**
     * @return bool
     */
    public static function isCloud():bool
    {
        return (bool)\Includes\Utils\ConfigParser::getOptions(['service', 'is_cloud']);
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Onboarding Wizard');
    }

    protected function doActionReset()
    {
        WizardState::getInstance()->reset();

        $this->setHardRedirect(true);
        $this->setReturnURL($this->buildURL('onboarding_wizard'));
    }

    protected function doActionMinimizeWizard()
    {
        WizardState::getInstance()->updateConfigOption('wizard_state', 'minimized');

        $this->setHardRedirect(true);
        $this->setReturnURL($this->buildURL('main'));
    }

    protected function doActionDisableWizard()
    {
        WizardState::getInstance()->updateConfigOption('wizard_state', 'disabled');
        WizardState::getInstance()->updateConfigOption('wizard_force_disabled', true);

        $this->setHardRedirect(true);
        $this->setReturnURL($this->buildURL('main'));
    }

    protected function doActionGoToStep()
    {
        $step = \XLite\Core\Request::getInstance()->step;

        $steps = $this->getWizardSteps();
        if (isset($steps[$step])) {
            WizardState::getInstance()->setCurrentStep($step);

        }

        $this->setReturnURL($this->buildURL('onboarding_wizard'));
    }

    /**
     * Returns current wizard state
     */
    public function getWizardState()
    {
        return Config::getInstance()->XC->Onboarding->wizard_state;
    }

    /**
     * Returns last added product id
     */
    public function getLastAddedProductId()
    {
        return WizardState::getInstance()->getLastAddedProductId();
    }

    /**
     * Returns current wizard step
     */
    public function getWizardStep()
    {
        return WizardState::getInstance()->getCurrentStep();
    }

    public function getWizardProgress()
    {
        return WizardState::getInstance()->getWizardProgress();
    }

    public function getWizardSteps()
    {
        $steps = WizardState::getInstance()->defineWizardSteps();

        if (!Main::isModuleEnabled('CDev\SimpleCMS')) {
            unset($steps['company_logo'], $steps['company_logo_added']);
        }

        return $steps;
    }

    protected function doActionAddProduct()
    {
        $this->silent = true;
        $this->setSuppressOutput(true);
        $dto = $this->getFormModelObject();
        $product = $this->getProduct();
        $isPersistent = $product->isPersistent();

        $formModel = new FormModelSimplified(['object' => $dto]);

        $form = $formModel->getForm();
        $data = \XLite\Core\Request::getInstance()->getData();
        $rawData = \XLite\Core\Request::getInstance()->getNonFilteredData();

        $form->submit($data[$this->formName]);

        if ($form->isValid()) {
            $dto->populateTo($product, $rawData[$this->formName]);
            \XLite\Core\Database::getEM()->persist($product);
            \XLite\Core\Database::getEM()->flush();

            $dto->afterPopulate($product, $rawData[$this->formName]);
            if (!$isPersistent) {
                $dto->afterCreate($product, $rawData[$this->formName]);

            } else {
                $dto->afterUpdate($product, $rawData[$this->formName]);
            }
            \XLite\Core\Database::getEM()->flush();

            WizardState::getInstance()->setLastAddedProductId($product->getProductId());

            $this->displayJSON([
                'product' => $product->getProductId(),
                'productName' => $product->getName(),
                'productImage'=> $product->getImage() ? $product->getImage()->getResizedURL(ProductAddedView::PARAM_ICON_MAX_WIDTH, ProductAddedView::PARAM_ICON_MAX_HEIGHT)[3] : ''
            ]);

        } else {
            $this->saveFormModelTmpData($rawData[$this->formName]);
            WizardState::getInstance()->setLastAddedProductId(null);

            $this->headerStatus(400);
            $this->displayJSON([
                'errors' => $this->buildErrorArray($form),
            ]);
        }
    }

    public function doActionUpdateLocation()
    {
        $this->silent = true;
        $this->setSuppressOutput(true);

        $countryCode = \Xlite\Core\Request::getInstance()->country;
        $currencyId = \Xlite\Core\Request::getInstance()->currency;
        $weightUnit = \Xlite\Core\Request::getInstance()->weight_unit;

        if ($country = \XLite\Core\Database::getRepo('XLite\Model\Country')->find($countryCode)) {
            \XLite\Core\Database::getRepo('\XLite\Model\Config')->createOption([
                'category' => 'Company',
                'name'     => 'location_country',
                'value'    => $country->getCode(),
            ]);
        }

        if ($currency = \XLite\Core\Database::getRepo('XLite\Model\Currency')->find($currencyId)) {
            \XLite\Core\Database::getRepo('\XLite\Model\Config')->createOption([
                'category' => 'General',
                'name'     => 'shop_currency',
                'value'    => $currency->getCurrencyId(),
            ]);
        }

        if (in_array($weightUnit, ['lbs', 'oz', 'kg', 'g'], true)) {
            \XLite\Core\Database::getRepo('\XLite\Model\Config')->createOption([
                'category' => 'Units',
                'name'     => 'weight_unit',
                'value'    => $weightUnit,
            ]);
        }
    }

    public function doActionUpdateCompanyInfo()
    {
        $this->silent = true;
        $this->setSuppressOutput(true);

        $request = \XLite\Core\Request::getInstance();

        \XLite\Core\Database::getRepo('\XLite\Model\Config')->createOptions([
            [
                'category' => 'Company',
                'name'     => 'company_name',
                'value'    => $request->company_name,
            ],
            [
                'category' => 'Company',
                'name'     => 'location_address',
                'value'    => $request->address,
            ],
            [
                'category' => 'Company',
                'name'     => 'location_address',
                'value'    => $request->address,
            ],
            [
                'category' => 'Company',
                'name'     => 'company_phone',
                'value'    => $request->phone,
            ],
            [
                'category' => 'Company',
                'name'     => 'location_city',
                'value'    => $request->city,
            ],
            [
                'category' => 'Company',
                'name'     => 'location_zipcode',
                'value'    => $request->zipcode,
            ],
        ]);

        $countryCode = \XLite\Core\Config::getInstance()->Company->location_country;

        $country = \XLite\Core\Database::getRepo('XLite\Model\Country')->find($countryCode);

        if ($country && $country->hasStates() && !$country->isForcedCustomState()) {
            \XLite\Core\Database::getRepo('\XLite\Model\Config')->createOption([
                'category' => 'Company',
                'name'     => 'location_state',
                'value'    => $request->address_state_select,
            ]);
        } else {
            \XLite\Core\Database::getRepo('\XLite\Model\Config')->createOption([
                'category' => 'Company',
                'name'     => 'location_custom_state',
                'value'    => $request->address_custom_state,
            ]);
        }
    }

    public function doActionUpdateBusinessInfo()
    {
        $this->silent = true;
        $this->setSuppressOutput(true);

        $request = \XLite\Core\Request::getInstance();

        \XLite\Core\Database::getRepo('\XLite\Model\Config')->createOptions([
            [
                'category' => 'Company',
                'name'     => 'company_name',
                'value'    => $request->company_name,
            ],
            [
                'category' => 'Company',
                'name'     => 'company_phone',
                'value'    => $request->phone,
            ],
            [
                'category' => 'Company',
                'name'     => 'sell_experience',
                'value'    => $request->experience,
            ],
            [
                'category' => 'Company',
                'name'     => 'business_category',
                'value'    => $request->category,
            ],
            [
                'category' => 'Company',
                'name'     => 'business_revenue',
                'value'    => $request->revenue,
            ],
        ]);

       $revenueFiled = !empty($request->revenue) || in_array($request->experience, ['not_selling', 'building_for_another']);

        $fullFilled = !empty($request->company_name)
            && !empty($request->phone)
            && !empty($request->experience)
            && !empty($request->category)
            && $revenueFiled
        ;

        $this->displayJSON([
            'full-filled' => $fullFilled,
            'onboarding-company-name' => $request->company_name,
            'onboarding-revenue' => $request->revenue,
            'onboarding-industry' => $request->category,
            'onboarding-experience' => $request->experience,
        ]);
    }

    public function doActionEnableShipping()
    {
        \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption([
            'category' => 'General',
            'name' => 'requires_shipping_default',
            'value' => true,
        ]);
    }

    public function doActionDisableShipping()
    {
        \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption([
            'category' => 'General',
            'name' => 'requires_shipping_default',
            'value' => false,
        ]);

        $qb = \XLite\Core\Database::getRepo('XLite\Model\Product')->createQueryBuilder();
        $qb->update()
            ->set($qb->getMainAlias() . '.free_shipping', $qb->expr()->literal(true));
        $qb->execute();
    }

    public function doActionCreateShippingMethod()
    {
        $this->silent = true;
        $this->setSuppressOutput(true);

        $zone = \XLite\Core\Database::getRepo('XLite\Model\Zone')->find(\XLite\Core\Request::getInstance()->zone_id);

        if ($zone) {
            $methodsRepo = \XLite\Core\Database::getRepo('XLite\Model\Shipping\Method');
            /** @var \XLite\Model\Shipping\Method $method */
            $method = $methodsRepo->findOneBy(['code' => static::SHIPPING_METHOD_CODE])
                ?: $methodsRepo->insert(null, false);

            $name = \XLite\Core\Request::getInstance()->method_label ?: static::t('My Shipping');

            $method->setEnabled(true);
            $method->setAdded(true);
            $method->setProcessor('offline');
            $method->setName($name);
            $method->setCode(static::SHIPPING_METHOD_CODE);

            $markupsRepo = \XLite\Core\Database::getRepo('XLite\Model\Shipping\Markup');
            /** @var \XLite\Model\Shipping\Markup $markup */
            $markup = $markupsRepo->findOneBy([
                'zone' => $zone,
                'shipping_method' => $method
            ]) ?: $markupsRepo->insert(null, false);

            $markup->setZone($zone);
            $markup->setShippingMethod($method);
            $markup->setMarkupFlat((float)\XLite\Core\Request::getInstance()->flat_rate);

            \XLite\Core\Database::getEM()->flush();
        }
    }

    protected function doActionRemoveDemoCatalog()
    {
        if ($this->isAJAX()) {
            $this->silent = true;
            $this->setSuppressOutput(true);
        }

        WizardState::getInstance()->deleteDemoCatalog();
    }

    protected function doActionUploadCompanyLogo()
    {
        $this->silent = true;
        $this->setSuppressOutput(true);

        $result = $this->uploadCompanyLogo();

        if ($result) {
            \XLite\Core\Database::getRepo('\XLite\Model\Config')->createOption([
                'category' => 'CDev\SimpleCMS',
                'name'     => 'logo',
                'value'    => $result
            ]);

            $this->displayJSON([
                'logo' => $result
            ]);
        } else {
            $this->headerStatus(400);
            $this->displayJSON([
                'errors' => static::t('Could not save logo image'),
            ]);
        }
    }

    protected function uploadCompanyLogo()
    {
        if (!Main::isModuleEnabled('CDev\SimpleCMS')) {
            return false;
        }

        $data = \XLite\Core\Request::getInstance()->company_logo;
        $optionValue = \XLite\Core\Config::getInstance()->CDev->SimpleCMS->logo;

        /** @var \XLite\Model\TemporaryFile $temporaryFile */
        $temporaryFile = isset($data['temp_id'])
            ? \XLite\Core\Database::getRepo('\XLite\Model\TemporaryFile')->find($data['temp_id'])
            : null;

        if ($temporaryFile) {
            $imageType = 'logo';

            $subDir = \Includes\Utils\FileManager::getRelativePath(LC_DIR_IMAGES, LC_DIR) . LC_DS . 'simplecms' . LC_DS;
            $dir = LC_DIR . LC_DS . $subDir;
            $path = null;

            $realName = preg_replace('/([^a-zA-Z0-9_\-\.]+)/', '_', $temporaryFile->getFileName());
            $realName = $imageType . '_' . $realName;

            if ($temporaryFile->isImage()) {
                if (\Includes\Utils\FileManager::isDirWriteable($dir) || \Includes\Utils\FileManager::mkdir($dir)) {

                    // Move uploaded file to destination directory
                    $path = \Includes\Utils\FileManager::move(
                        $temporaryFile->getStoragePath(),
                        $dir . LC_DS . $realName,
                        true
                    );

                    if ($path) {
                        if ($optionValue && basename($optionValue) !== $realName) {
                            // Remove old image file
                            \Includes\Utils\FileManager::deleteFile($dir . basename($optionValue));
                        }

                        $optionValue = $subDir . $realName;
                    }
                }

                if (!isset($path)) {
                    return false;
                }

            } else {
                return false;
            }
        }

        return $optionValue;
    }

    public function buildErrorArray(FormInterface $form)
    {
        $errors = [];

        foreach ($form->all() as $child) {
            $errors[] = $this->buildErrorArray($child);
        }

        $errors = array_merge(...$errors);

        foreach ($form->getErrors() as $error) {
            $errors[$error->getCause()->getPropertyPath()] = $error->getMessage();
        }

        return $errors;
    }

    /**
     * @return DTOSimplified
     */
    public function getFormModelObject()
    {
        return new DTOSimplified($this->getProduct());
    }

    /**
     * Alias
     *
     * @return Product
     */
    public function getProduct()
    {
        $request = \XLite\Core\Request::getInstance();

        $defaultProductData = [
            'name' => $request->Wizard_product_name ?? '',
            'price' => $request->Wizard_product_price
                ? (float) $request->Wizard_product_price
                : 0,
        ];

        $request->unsetCookie('Wizard_product_name');
        $request->unsetCookie('Wizard_product_price');


        return new Product($defaultProductData);
    }
}