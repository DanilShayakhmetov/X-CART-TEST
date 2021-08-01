<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Onboarding\View\FormModel\Product;

/**
 * Product form model
 */
class Simplified extends \XLite\View\FormModel\AFormModel
{
    /**
     * Do not render form_start and form_end in null returned
     *
     * @return string|null
     */
    protected function getTarget()
    {
        return 'onboarding_wizard';
    }

    /**
     * @return string
     */
    protected function getAction()
    {
        return 'add_product';
    }

    /**
     * @return array
     */
    public function getCSSFiles()
    {
        return array_merge(
            parent::getCSSFiles(),
            [
                'form_model/product/style.less',
                'modules/XC/Onboarding/form_model/product/simplified.css'
            ]
        );
    }

    /**
     * @return array
     */
    protected function defineFields()
    {
        $nameMaxLength = \XLite\Core\Database::getRepo('XLite\Model\ProductTranslation')->getFieldInfo('name', 'length');
        $image         = null;
        $request       = \XLite\Core\Request::getInstance();

        if ($imageTempId = $request->Wizard_product_image_temp_id) {
            $image = \XLite\Core\Database::getRepo('XLite\Model\TemporaryFile')->find($imageTempId);

            if ($image) {
                $image->setAlt(
                    $request->Wizard_product_image_alt ?? ''
                );
            }

            $request->unsetCookie('Wizard_product_image_temp_id');
            $request->unsetCookie('Wizard_product_image_alt');
        }

        $currency = \XLite::getInstance()->getCurrency();
        $currencySymbol = $currency->getCurrencySymbol(false);

        $schema = [
            self::SECTION_DEFAULT => [
                'images'        => [
                    'label'         => false,
                    'type'          => 'XLite\View\FormModel\Type\UploaderType',
                    'imageClass'    => 'XLite\Model\Image\Product\Image',
                    'helpMessage'   => static::t('Click here to upload product image'),
                    'files'         => $image,
                    'multiple'      => false,
                    'previewWidth'  => 178,
                    'previewHeight' => 178,
                    'position'      => 100,
                ],
                'name'          => [
                    'label'       => false,
                    'attr'        => [
                        'placeholder' => static::t('Enter product name') . '*'
                    ],
                    'constraints' => [
                        'XLite\Core\Validator\Constraints\MaxLength'       => [
                            'length'  => $nameMaxLength,
                            'message' =>
                                static::t('Name length must be less then {{length}}', ['length' => $nameMaxLength + 1]),
                        ],
                    ],
                    'position'    => 200,
                ],
                'price'         => [
                    'label'             => false,
                    'attr'              => [
                        'placeholder' => static::t('Price')
                    ],
                    'type'              => 'XLite\View\FormModel\Type\SymbolType',
                    'symbol'            => $currencySymbol,
                    'inputmask_pattern' => [
                        'alias'      => 'xcdecimal',
                        'prefix'     => '',
                        'rightAlign' => false,
                        'digits'     => $currency->getE(),
                    ],
                    'constraints'       => [
                        'Symfony\Component\Validator\Constraints\GreaterThanOrEqual' => [
                            'value'   => 0,
                            'message' => static::t('Minimum value is X', ['value' => 0]),
                        ],
                    ],
                    'position'          => 300,
                ],
                'other_options' => [
                    'label'    => false,
                    'type'     => 'XLite\Module\XC\Onboarding\View\FormModel\Type\TemplateType',
                    'template' => 'modules/XC/Onboarding/form_model/product/other_options.twig',
                    'position' => 400
                ],
                'import_link' => [
                    'label'    => false,
                    'type'     => 'XLite\Module\XC\Onboarding\View\FormModel\Type\TemplateType',
                    'template' => 'modules/XC/Onboarding/form_model/product/import_link.twig',
                    'position' => 500
                ]
            ],
        ];

        return $schema;
    }

    /**
     * @return string
     */
    protected function getOtherOptionsUrl()
    {
        return \XLite::getController()->buildURL('product');
    }

    /**
     * Return list of the "Button" widgets
     *
     * @return array
     */
    protected function getFormButtons()
    {
        $result = parent::getFormButtons();

        $result['submit'] = new \XLite\View\Button\Submit(
            [
                \XLite\View\Button\AButton::PARAM_LABEL    => 'Save and go to the next step',
                \XLite\View\Button\AButton::PARAM_BTN_TYPE => 'regular-main-button',
                \XLite\View\Button\AButton::PARAM_STYLE    => 'action',
                \XLite\View\FormField\AFormField::PARAM_ATTRIBUTES => [
                    'data-dirty' => static::t('Save and go to the next step'),
                    'data-pristine' => static::t('Skip this step')
                ]
            ]
        );

        return $result;
    }

    /**
     * Return class of button panel widget
     *
     * @return string
     */
    protected function getButtonPanelClass()
    {
        return 'XLite\Module\XC\Onboarding\View\StickyPanel\FormModel\NonStickyPanel';
    }
}
