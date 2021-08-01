<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormModel\Product;

use XLite\Core\Config;
use XLite\Core\Converter;
use XLite\Core\Database;
use XLite\Core\Translation;
use XLite\View\Button\AButton;
use XLite\View\Button\Link;
use XLite\View\Button\SimpleLink;
use XLite\View\Button\Submit;
use XLite\View\FormField\Select\FloatFormat;

/**
 * Product form model
 */
class Info extends \XLite\View\FormModel\AFormModel
{
    /**
     * Do not render form_start and form_end in null returned
     *
     * @return string|null
     */
    protected function getTarget()
    {
        return 'product';
    }

    /**
     * @return string
     */
    protected function getAction()
    {
        return 'update';
    }

    /**
     * @return array
     */
    protected function getActionParams()
    {
        $identity = $this->getDataObject()->default->identity;

        return $identity ? ['product_id' => $identity] : [];
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
            ]
        );
    }

    /**
     * @return array
     */
    protected function defineSections()
    {
        return array_replace(parent::defineSections(), [
            'prices_and_inventory' => [
                'label'    => static::t('Prices & Inventory'),
                'position' => 100,
            ],
            'shipping'             => [
                'label'    => static::t('Shipping'),
                'position' => 200,
            ],
            'marketing'            => [
                'label'    => static::t('Marketing'),
                'position' => 300,
            ],
        ]);
    }

    /**
     * @return array
     */
    protected function defineFields()
    {
        $skuMaxLength = Database::getRepo('XLite\Model\Product')->getFieldInfo('sku', 'length');
        $nameMaxLength = Database::getRepo('XLite\Model\ProductTranslation')->getFieldInfo('name', 'length');
        $metaTagsMaxLength = Database::getRepo('XLite\Model\ProductTranslation')->getFieldInfo('metaTags', 'length');
        $metaTitleMaxLength = Database::getRepo('XLite\Model\ProductTranslation')->getFieldInfo('metaTitle', 'length');

        $memberships = [];
        foreach (Database::getRepo('XLite\Model\Membership')->findActiveMemberships() as $membership) {
            $memberships[$membership->getMembershipId()] = $membership->getName();
        }

        $taxClasses = [];
        foreach (Database::getRepo('XLite\Model\TaxClass')->findAll() as $taxClass) {
            $taxClasses[$taxClass->getId()] = $taxClass->getName();
        }

        $taxClassSchema = [
            'label'       => static::t('Tax class'),
            'description' => static::t(
                'Tax classes link',
                ['link' => $this->buildURL('tax_classes')]
            ),
            'position'    => 200,
        ];
        if ($taxClasses) {
            $taxClassSchema = array_replace(
                $taxClassSchema,
                [
                    'type'              => 'Symfony\Component\Form\Extension\Core\Type\ChoiceType',
                    'choices'           => array_flip($taxClasses),
                    'placeholder'       => static::t('Default'),
                ]
            );
        } else {
            $taxClassSchema = array_replace(
                $taxClassSchema,
                [
                    'type'    => 'XLite\View\FormModel\Type\CaptionType',
                    'caption' => static::t('Default'),
                ]
            );
        }

        $currency = \XLite::getInstance()->getCurrency();
        $currencySymbol = $currency->getCurrencySymbol(false);

        $weightFormat = Config::getInstance()->Units->weight_format;
        $weightFormatDelimiters = FloatFormat::getDelimiters($weightFormat);

        $inventoryTrackingDescription = $this->getDataObject()->default->identity && $this->getInventoryTrackingDescriptionTemplate()
            ? $this->getWidget([
                'template' => $this->getInventoryTrackingDescriptionTemplate(),
            ])->getContent()
            : '';

        $product = Database::getRepo('XLite\Model\Product')->find($this->getDataObject()->default->identity);
        $images = [];
        if ($product) {
            $images = $product->getImages();
        }

        $cleanUrlExt = \XLite\Model\Repo\CleanURL::isProductUrlHasExt() ? '.html' : '';

        if ($product
            && $product->getCleanURL()
            && \XLite\Model\Repo\CleanURL::isProductUrlHasExt()
            && !preg_match('/.html$/', $product->getCleanURL())
        ) {
            $cleanUrlExt = '';
        }

        $schema = [
            self::SECTION_DEFAULT  => [
                'name'               => [
                    'label'       => static::t('Product name'),
                    'required'    => true,
                    'constraints' => [
                        'Symfony\Component\Validator\Constraints\NotBlank' => [
                            'message' => static::t('This field is required'),
                        ],
                        'XLite\Core\Validator\Constraints\MaxLength'       => [
                            'length'  => $nameMaxLength,
                            'message' =>
                                static::t('{{field}} length must be less then {{length}}', ['field'  => static::t('Product name'),
                                                                                            'length' => $nameMaxLength + 1]),
                        ],
                    ],
                    'position'    => 100,
                ],
                'sku'                => [
                    'label'       => static::t('SKU'),
                    'constraints' => [
                        'XLite\Core\Validator\Constraints\MaxLength' => [
                            'length'  => $skuMaxLength,
                            'message' =>
                                static::t('SKU length must be less then {{length}}', ['length' => $skuMaxLength + 1]),
                        ],
                    ],
                    'position'    => 200,
                ],
                'images'             => [
                    'label'      => static::t('Images'),
                    'type'       => 'XLite\View\FormModel\Type\UploaderType',
                    'imageClass' => 'XLite\Model\Image\Product\Image',
                    'files'      => $images,
                    'multiple'   => true,
                    'position'   => 300,
                ],
                'category'           => [
                    'label'    => static::t('Category'),
                    'type'     => 'XLite\View\FormModel\Type\ProductCategoryType',
                    'multiple' => true,
                    'position' => 400,
                ],
                'description'        => [
                    'label'    => static::t('Description'),
                    'type'     => 'XLite\View\FormModel\Type\TextareaAdvancedType',
                    'position' => 500,
                ],
                'full_description'   => [
                    'label'    => static::t('Full description'),
                    'type'     => 'XLite\View\FormModel\Type\TextareaAdvancedType',
                    'position' => 600,
                ],
                'available_for_sale' => [
                    'label'    => static::t('Available for sale'),
                    'help'     => static::t('If the product is not available for sale, the system will return 404 response.'),
                    'type'     => 'XLite\View\FormModel\Type\SwitcherType',
                    'position' => 700,
                ],
            ],
            'prices_and_inventory' => [
                'promo'              => [
                    'type'    => 'XLite\View\FormModel\Type\PromoType',
                    'promoId' => 'wholesale-prices-1',
                ],
                'arrival_date'       => [
                    'label'       => static::t('Arrival date'),
                    'type'        => 'XLite\View\FormModel\Type\DatepickerType',
                    'position'    => 350,
                    'constraints' => [
                        'XLite\Core\Validator\Constraints\Timestamp' => [
                            'message' => static::t('[field] year must be between 1970 and 2038', ['field' => static::t('Arrival date')])
                        ],
                    ],
                ],
                'memberships'        => [
                    'label'    => static::t('Memberships'),
                    'type'     => 'XLite\View\FormModel\Type\Select2Type',
                    'multiple' => true,
                    'choices'  => array_flip($memberships),
                    'position' => 100,
                ],
                'tax_class'          => $taxClassSchema,
                'price'              => [
                    'label'             => static::t('Price'),
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
                'inventory_tracking' => [
                    'label'       => static::t('Inventory tracking for this product is'),
                    'description' => $inventoryTrackingDescription,
                    'type'        => 'XLite\View\FormModel\Type\InventoryTrackingType',
                    'default'     => $this->getDataObject()->prices_and_inventory->inventory_tracking->quantity,
                    'position'    => 400,
                ],
            ],
            'shipping'             => [
                'weight'            => [
                    'label'             => static::t('Weight'),
                    'type'              => 'XLite\View\FormModel\Type\SymbolType',
                    'symbol'            => Config::getInstance()->Units->weight_symbol,
                    'inputmask_pattern' => [
                        'alias'          => 'xcdecimal',
                        'digitsOptional' => false,
                        'rightAlign'     => false,
                        'digits'         => 4,
                    ],
                    'position'          => 100,
                ],
                'requires_shipping' => [
                    'label'    => static::t('Requires shipping'),
                    'type'     => 'XLite\View\FormModel\Type\Base\CompositeType',
                    'position' => 200,
                    'fields'   => [
                        'requires_shipping' => [
                            'label'    => static::t('Requires shipping'),
                            'type'     => 'XLite\View\FormModel\Type\SwitcherType',
                            'position' => 100,
                        ],
                        'shipping_box'      => [
                            'type'             => 'XLite\View\FormModel\Type\Base\CompositeType',
                            'show_label_block' => false,
                            'fields'           => [
                                'separate_box' => [
                                    'label'    => static::t('Separate box'),
                                    'type'     => 'Symfony\Component\Form\Extension\Core\Type\CheckboxType',
                                    'position' => 100,
                                ],
                                'dimensions'   => [
                                    'label'     => static::t('Length x Width x Height') . ' (' . Translation::translateDimSymbol() . ')',
                                    'type'      => 'XLite\View\FormModel\Type\DimensionsType',
                                    'show_when' => [
                                        '..' => [
                                            'separate_box' => 1,
                                        ],
                                    ],
                                    'position'  => 200,
                                ],
                                'items_in_box' => [
                                    'label'             => static::t('Maximum items in box'),
                                    'type'              => 'XLite\View\FormModel\Type\PatternType',
                                    'inputmask_pattern' => [
                                        'alias'      => 'integer',
                                        'rightAlign' => false,
                                    ],
                                    'show_when'         => [
                                        '..' => [
                                            'separate_box' => 1,
                                        ],
                                    ],
                                    'position'          => 300,
                                ],
                            ],
                            'show_when'        => [
                                '..' => [
                                    'requires_shipping' => '1',
                                ],
                            ],
                            'position'         => 500,
                        ],
                    ],
                ],
            ],
            'marketing'            => [
                'meta_description_type' => [
                    'label'       => static::t('Meta description'),
                    'type'        => 'Symfony\Component\Form\Extension\Core\Type\ChoiceType',
                    'choices'     => array_flip([
                        'A' => (string)static::t('Autogenerated'),
                        'C' => (string)static::t('Custom'),
                    ]),
                    'placeholder' => false,
                    'position'    => 100,
                ],
                'meta_description'      => [
                    'label'              => ' ',
                    'type'               => 'XLite\View\FormModel\Type\MetaDescriptionType',
                    'required'           => true,
                    'constraints'        => [
                        'XLite\Core\Validator\Constraints\MetaDescription' => [
                            'message'          => static::t('This field is required'),
                            'dependency'       => 'form.marketing.meta_description_type',
                            'dependency_value' => 'C',
                        ],
                    ],
                    'validation_trigger' => 'form.marketing.meta_description_type',
                    'show_when'          => [
                        'marketing' => [
                            'meta_description_type' => 'C',
                        ],
                    ],
                    'position'           => 200,
                ],
                'meta_keywords'         => [
                    'label'       => static::t('Meta keywords'),
                    'position'    => 300,
                    'constraints' => [
                        'XLite\Core\Validator\Constraints\MaxLength' => [
                            'length'  => $metaTagsMaxLength,
                            'message' =>
                                static::t('{{field}} length must be less then {{length}}', ['field'  => static::t('Meta keywords'),
                                                                                            'length' => $metaTagsMaxLength + 1]),
                        ],
                    ],
                ],
                'product_page_title'    => [
                    'label'       => static::t('Product page title'),
                    'description' => static::t('Leave blank to use product name as Page Title.'),
                    'position'    => 400,
                    'constraints' => [
                        'XLite\Core\Validator\Constraints\MaxLength' => [
                            'length'  => $metaTitleMaxLength,
                            'message' =>
                                static::t('{{field}} length must be less then {{length}}', ['field'  => static::t('Product page title'),
                                                                                            'length' => $metaTitleMaxLength + 1]),
                        ],
                    ],
                ],
                'clean_url'             => [
                    'label'           => static::t('Clean URL'),
                    'type'            => 'XLite\View\FormModel\Type\CleanURLType',
                    'extension'       => $cleanUrlExt,
                    'objectClassName' => 'XLite\Model\Product',
                    'objectId'        => $this->getDataObject()->default->identity,
                    'objectIdName'    => 'product_id',
                    'position'        => 500,
                ],
            ],
        ];

        if ((boolean)$this->getDataObject()->default->identity) {
            $schema[self::SECTION_DEFAULT]['sku']['required'] = true;
            $schema[self::SECTION_DEFAULT]['sku']['constraints']['Symfony\Component\Validator\Constraints\NotBlank'] = [
                'message' => static::t('This field is required'),
            ];
            $schema[self::SECTION_DEFAULT]['available_for_sale']['help'] = static::t(
                'If the product is not available for sale, the system will return 404.',
                ['productLink' => $product->getFrontURL(false, true)]
            );
        }

        return $schema;
    }

    /**
     * Return list of the "Button" widgets
     *
     * @return array
     */
    protected function getFormButtons()
    {
        $result = parent::getFormButtons();
        $identity = $this->getDataObject()->default->identity;

        $label = $identity ? 'Update product' : 'Add product';
        $result['submit'] = new Submit(
            [
                AButton::PARAM_LABEL    => $label,
                AButton::PARAM_BTN_TYPE => 'regular-main-button',
                AButton::PARAM_STYLE    => 'action',
            ]
        );

        if ($identity) {
            $url = $this->buildURL(
                'product',
                'clone',
                ['product_id' => $identity]
            );
            $result['clone-product'] = new Link(
                [
                    AButton::PARAM_LABEL => 'Clone this product',
                    AButton::PARAM_STYLE => 'model-button always-enabled',
                    Link::PARAM_LOCATION => $url,
                ]
            );

            $result['preview-product'] = new SimpleLink(
                [
                    AButton::PARAM_LABEL => 'Preview product page',
                    AButton::PARAM_STYLE => 'model-button link action',
                    Link::PARAM_BLANK    => true,
                    Link::PARAM_LOCATION => $this->getProductPreviewURL(),
                ]
            );
        }

        return $result;
    }

    /**
     * @return string
     */
    protected function getProductPreviewURL()
    {
        $identity = $this->getDataObject()->default->identity;
        return Converter::buildURL(
            'product',
            'preview',
            [
                'product_id' => $identity,
                'shopKey'    => \XLite\Core\Auth::getInstance()->getShopKey(),
            ],
            \XLite::getCustomerScript()
        );
    }

    protected function getProductEntity()
    {
        return Database::getRepo('XLite\Model\Product')->find($this->getDataObject()->default->identity);
    }

    /**
     * @return string
     */
    protected function getInventoryTrackingDescriptionTemplate()
    {
        return 'form_model/product/info/inventory_tracking_description.twig';
    }

    protected function getInventoryTrackingURL()
    {
        $identity = $this->getDataObject()->default->identity;

        return $this->buildURL(
            'product',
            '',
            [
                'product_id' => $identity,
                'page'       => 'inventory',
            ]
        );
    }
}
