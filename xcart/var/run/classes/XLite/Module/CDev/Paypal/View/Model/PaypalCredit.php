<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\View\Model;

use \XLite\Module\CDev\Paypal;
use XLite\Module\CDev\Paypal\View\FormField\Select\PPCMBannerType;

/**
 * ExpressCheckout
 */
class PaypalCredit extends \XLite\View\Model\AModel
{
    const PARAM_PAYMENT_METHOD = 'paymentMethod';

    /**
     * Schema of the default section
     *
     * @var array
     */
    protected $schemaDefault = [
        'enabled' => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Select\EnabledDisabled',
            self::SCHEMA_LABEL    => 'PayPal Credit is',
            self::SCHEMA_REQUIRED => false,
        ],
    ];

    /**
     * Get CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/CDev/Paypal/settings/payments_style_credit.less';

        return $list;
    }

    /**
     * Return model object to use
     *
     * @return \XLite\Model\Payment\Method
     */
    public function getModelObject()
    {
        return $this->getPaymentMethod();
    }

    /**
     * Return list of form fields for section default
     *
     * @return array
     */
    protected function getFormFieldsForSectionDefault()
    {
        if (\XLite::getController() instanceof \XLite\Module\CDev\Paypal\Controller\Admin\PaypalCommercePlatformCredit) {
            $this->schemaDefault = array_merge(
                $this->schemaDefault,
                $this->getPPCMFields()
            );
        }

        if (\XLite::getController() instanceof \XLite\Module\CDev\Paypal\Controller\Admin\PaypalCredit) {
            $this->schemaDefault = array_merge(
                $this->schemaDefault,
                $this->getLegacyCreditFields()
            );
        }

        return $this->translateSchema('default');
    }

    /**
     * PayPal credit messaging options fields
     *
     * @return array
     */
    protected function getPPCMFields()
    {
        return [
            'ppcm_enabled'  => [
                self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Checkbox\OnOff',
                self::SCHEMA_LABEL    => 'PayPal Credit Messaging',
                self::SCHEMA_DEPENDENCY => [
                    self::DEPENDENCY_SHOW => [
                        'enabled' => true,
                    ],
                ],
            ],
            'ppcm_product_page'   => [
                self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Checkbox\OnOff',
                self::SCHEMA_LABEL    => 'Banner on product pages',
                self::SCHEMA_DEPENDENCY => [
                    self::DEPENDENCY_SHOW => [
                        'ppcm_enabled' => true,
                        'enabled' => true,
                    ],
                ],
            ],
            'ppcm_cart_page'   => [
                self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Checkbox\OnOff',
                self::SCHEMA_LABEL    => 'Banner on the cart page',
                self::SCHEMA_DEPENDENCY => [
                    self::DEPENDENCY_SHOW => [
                        'ppcm_enabled' => true,
                        'enabled' => true,
                    ],
                ],
            ],
            'ppcm_checkout_page'   => [
                self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Checkbox\OnOff',
                self::SCHEMA_LABEL    => 'Banner on the checkout page',
                self::SCHEMA_DEPENDENCY => [
                    self::DEPENDENCY_SHOW => [
                        'ppcm_enabled' => true,
                        'enabled' => true,
                    ],
                ],
            ],
            'ppcm_banner_type'   => [
                self::SCHEMA_CLASS    => 'XLite\Module\CDev\Paypal\View\FormField\Select\PPCMBannerType',
                self::SCHEMA_LABEL    => 'Banner type',
                self::SCHEMA_HELP     => 'Choose text if you need a lightweight contextual message. Choose flex if you need a responsive banner',
                self::SCHEMA_DEPENDENCY => [
                    self::DEPENDENCY_SHOW => [
                        'ppcm_enabled' => true,
                        'enabled' => true,
                    ],
                ],
            ],
            'ppcm_text_logo_type'   => [
                self::SCHEMA_CLASS    => 'XLite\Module\CDev\Paypal\View\FormField\Select\PPCMTextLogoType',
                self::SCHEMA_LABEL    => 'Logo type',
                self::SCHEMA_DEPENDENCY => [
                    self::DEPENDENCY_SHOW => [
                        'ppcm_enabled' => true,
                        'enabled' => true,
                        'ppcm_banner_type' => PPCMBannerType::PPCM_TEXT
                    ],
                ],
            ],
            'ppcm_text_logo_position'   => [
                self::SCHEMA_CLASS    => 'XLite\Module\CDev\Paypal\View\FormField\Select\PPCMTextLogoPosition',
                self::SCHEMA_LABEL    => 'Logo position',
                self::SCHEMA_HELP     => 'Applicable to stacked and single line logos only',
                self::SCHEMA_DEPENDENCY => [
                    self::DEPENDENCY_SHOW => [
                        'ppcm_enabled' => true,
                        'enabled' => true,
                        'ppcm_banner_type' => PPCMBannerType::PPCM_TEXT
                    ],
                ],
            ],
            'ppcm_text_size'   => [
                self::SCHEMA_CLASS    => 'XLite\Module\CDev\Paypal\View\FormField\Select\PPCMTextSize',
                self::SCHEMA_LABEL    => 'Text size (ppcm)',
                self::SCHEMA_DEPENDENCY => [
                    self::DEPENDENCY_SHOW => [
                        'ppcm_enabled' => true,
                        'enabled' => true,
                        'ppcm_banner_type' => PPCMBannerType::PPCM_TEXT
                    ],
                ],
            ],
            'ppcm_text_color'   => [
                self::SCHEMA_CLASS    => 'XLite\Module\CDev\Paypal\View\FormField\Select\PPCMTextColor',
                self::SCHEMA_LABEL    => 'Text color (ppcm)',
                self::SCHEMA_DEPENDENCY => [
                    self::DEPENDENCY_SHOW => [
                        'ppcm_enabled' => true,
                        'enabled' => true,
                        'ppcm_banner_type' => PPCMBannerType::PPCM_TEXT
                    ],
                ],
            ],
            'ppcm_flex_color_scheme'   => [
                self::SCHEMA_CLASS    => 'XLite\Module\CDev\Paypal\View\FormField\Select\PPCMFlexColorScheme',
                self::SCHEMA_LABEL    => 'Color scheme (ppcm)',
                self::SCHEMA_DEPENDENCY => [
                    self::DEPENDENCY_SHOW => [
                        'ppcm_enabled' => true,
                        'enabled' => true,
                        'ppcm_banner_type' => PPCMBannerType::PPCM_FLEX
                    ],
                ],
            ],
            'ppcm_flex_layout'   => [
                self::SCHEMA_CLASS    => 'XLite\Module\CDev\Paypal\View\FormField\Select\PPCMFlexLayout',
                self::SCHEMA_LABEL    => 'Layout (ppcm)',
                self::SCHEMA_DEPENDENCY => [
                    self::DEPENDENCY_SHOW => [
                        'ppcm_enabled' => true,
                        'enabled' => true,
                        'ppcm_banner_type' => PPCMBannerType::PPCM_FLEX
                    ],
                ],
            ],
        ];
    }

    /**
     * Fields for PayPal Express Checkout (legacy)
     *
     * @return array
     */
    protected function getLegacyCreditFields()
    {
        return [
            'publisherId' => [
                self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text',
                self::SCHEMA_LABEL    => 'PayPal Publisher ID',
                self::SCHEMA_REQUIRED => true,
                self::SCHEMA_DEPENDENCY => [
                    self::DEPENDENCY_SHOW => [
                        'enabled' => ['1'],
                    ],
                ],
            ],
            'bannerOnHomePage' => [
                self::SCHEMA_CLASS    => 'XLite\Module\CDev\Paypal\View\FormField\Select\BannerOnHomePage',
                self::SCHEMA_LABEL    => 'Banner on Home page',
                self::SCHEMA_REQUIRED => false,
                self::SCHEMA_DEPENDENCY => [
                    self::DEPENDENCY_SHOW => [
                        'enabled' => ['1'],
                    ],
                ],
            ],
            'bannerOnCategoryPages' => [
                self::SCHEMA_CLASS    => 'XLite\Module\CDev\Paypal\View\FormField\Select\BannerOnCategoryPages',
                self::SCHEMA_LABEL    => 'Banner on Category pages',
                self::SCHEMA_REQUIRED => false,
                self::SCHEMA_DEPENDENCY => [
                    self::DEPENDENCY_SHOW => [
                        'enabled' => ['1'],
                    ],
                ],
            ],
            'bannerOnProductDetailsPages' => [
                self::SCHEMA_CLASS    => 'XLite\Module\CDev\Paypal\View\FormField\Select\BannerOnProductDetailsPages',
                self::SCHEMA_LABEL    => 'Banner on Product details pages',
                self::SCHEMA_REQUIRED => false,
                self::SCHEMA_DEPENDENCY => [
                    self::DEPENDENCY_SHOW => [
                        'enabled' => ['1'],
                    ],
                ],
            ],
            'bannerOnCartPage' => [
                self::SCHEMA_CLASS    => 'XLite\Module\CDev\Paypal\View\FormField\Select\BannerOnCartPage',
                self::SCHEMA_LABEL    => 'Banner on Cart page',
                self::SCHEMA_REQUIRED => false,
                self::SCHEMA_DEPENDENCY => [
                    self::DEPENDENCY_SHOW => [
                        'enabled' => ['1'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Return name of web form widget class
     *
     * @return string
     */
    protected function getFormClass()
    {
        return 'XLite\Module\CDev\Paypal\View\Form\PaypalCreditSettings';
    }

    /**
     * There is no object for settings
     *
     * @return \XLite\Model\AEntity
     */
    protected function getDefaultModelObject()
    {
        return null;
    }

    /**
     * Retrieve property from the model object
     *
     * @param mixed $name Field/property name
     *
     * @return mixed
     */
    protected function getModelObjectValue($name)
    {
        $paymentMethod = $this->getParam(static::PARAM_PAYMENT_METHOD);

        $value = $paymentMethod
            ? $paymentMethod->getSetting($name)
            : null;

        if ('email' === $name
            && empty($value)
            && $this->isExpressCheckoutEmailAPIType()
        ) {
            $value = $this->getExpressCheckoutEmail();
        }

        return $value;
    }

    /**
     * Get express checkout email
     *
     * @return string
     */
    protected function getExpressCheckoutEmail()
    {
        $expressCheckout = Paypal\Main::getPaymentMethod(Paypal\Main::PP_METHOD_EC);

        return $expressCheckout->getSetting('email');
    }

    /**
     * Get express checkout email
     *
     * @return string
     */
    protected function isExpressCheckoutEmailAPIType()
    {
        $expressCheckout = Paypal\Main::getPaymentMethod(Paypal\Main::PP_METHOD_EC);

        return 'email' === $expressCheckout->getSetting('api_type');
    }

    /**
     * defineWidgetParams
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            self::PARAM_PAYMENT_METHOD => new \XLite\Model\WidgetParam\TypeObject('Payment method', null),
        );
    }

    /**
     * Return class of button panel widget
     *
     * @return string
     */
    protected function getButtonPanelClass()
    {
        return 'XLite\View\StickyPanel\Payment\Settings';
    }

    /**
     * Populate model object properties by the passed data
     *
     * @param array $data Data to set
     *
     * @return void
     */
    protected function setModelProperties(array $data)
    {
        if (isset($data['enabled']) && !$data['enabled']) {
            $data = array('enabled' => '0');
        }

        foreach ($data as $name => $value) {
            switch ($name) {
                case 'agreement':
                    $value = !empty($value);
                    break;

                default:
                    break;
            }

            $this->getModelObject()->setSetting($name, $value);
        }
    }
}
