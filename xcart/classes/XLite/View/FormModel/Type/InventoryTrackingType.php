<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormModel\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use XLite\View\FormModel\Type\Base\AType;

/**
 * Class used for Categories type on product modify page.
 *
 * It lazy load there choices, so list populated only with selected values
 */
class InventoryTrackingType extends AType
{
    /**
     * @return string
     */
    public function getParent()
    {
        return 'XLite\View\FormModel\Type\Base\CompositeType';
    }

    /**
     * @param OptionsResolver $resolver
     *
     * @throws \Symfony\Component\OptionsResolver\Exception\AccessException
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'default'  => '',
        ]);
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('inventory_tracking', 'XLite\View\FormModel\Type\SwitcherType', [
            'show_label_block'  => false,
            'form_row_class'    => '',
        ]);
        $builder->add('quantity', 'XLite\View\FormModel\Type\PatternType', [
            'label'             => static::t('Quantity in stock'),
            'inputmask_pattern' => [
                'alias'      => 'integer',
                'rightAlign' => false,
            ],
            'show_when'         => [
                'prices_and_inventory' => [
                    'inventory_tracking' => [
                        'inventory_tracking' => '1',
                    ],
                ],
            ],
            'form_row_class'    => '',
        ]);
        $builder->add('quantity_origin', 'Symfony\Component\Form\Extension\Core\Type\HiddenType');
    }

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        foreach ($view->children as $child) {
            if (in_array($child->vars['name'], ['quantity', 'quantity_origin'])) {
                $child->vars['value'] = $options['default'];
            }
        }
    }
}
