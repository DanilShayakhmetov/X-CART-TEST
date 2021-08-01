<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\FreeShipping\View\FormModel\Product;

/**
 * Class Info
 */
class Info extends \XLite\View\FormModel\Product\Info implements \XLite\Base\IDecorator
{
    /**
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/XC/FreeShipping/form_model/product_info.less';

        return $list;
    }

    /**
     * @return array
     */
    protected function defineFields()
    {
        $schema = parent::defineFields();
        
        $schema = $this->defineFieldsFreeShipping($schema);
        
        return $schema;
    }
    
    protected function defineFieldsFreeShipping($schema)
    {
        $currency = \XLite::getInstance()->getCurrency();
        $currencySymbol = $currency->getCurrencySymbol(false);

        $schema = static::compose(
            $schema,
            [
                'shipping' => [
                    'requires_shipping' => [
                        'fixed_shipping_freight' => [
                            'label'             => static::t('Freight'),
                            'help'              => static::t('This field can be used to set a fixed shipping fee for the product. Make sure the field value is a positive number (greater than zero).'),
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
                            'show_when'         => [
                                '..' => [
                                    'requires_shipping' => true,
                                    'free_shipping'     => false,
                                    'ship_for_free'     => false,
                                ],
                            ],
                            'position'          => 200,
                        ],
                        'ship_for_free'          => [
                            'label'            => static::t('Free shipping'),
                            'show_label_block' => false,
                            'type'             => 'Symfony\Component\Form\Extension\Core\Type\CheckboxType',
                            'show_when'        => [
                                '..' => [
                                    'requires_shipping' => true,
                                ],
                            ],
                            'position'         => 300,
                        ],
                        'free_shipping'          => [
                            'label'            => static::t('Exclude from shipping cost calculation'),
                            'show_label_block' => false,
                            'type'             => 'Symfony\Component\Form\Extension\Core\Type\CheckboxType',
                            'show_when'        => [
                                '..' => [
                                    'requires_shipping' => true,
                                ],
                            ],
                            'position'         => 400,
                        ],
                    ],
                ],
            ]
        );
        
        return $schema;
    }
}
