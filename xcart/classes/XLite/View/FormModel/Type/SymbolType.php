<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormModel\Type;

use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use XLite\View\AView;
use XLite\View\FormModel\Type\Base\AType;

class SymbolType extends AType
{
    /**
     * @return array
     */
    public static function getCommonFiles()
    {
        return [
            AView::RESOURCE_JS => ['jquery.inputmask/dist/jquery.inputmask.bundle.js'],
        ];
    }

    /**
     * @return array
     */
    public static function getJSFiles()
    {
        return ['form_model/type/symbol_type.js'];
    }

    /**
     * @param OptionsResolver $resolver
     *
     * @throws \Symfony\Component\OptionsResolver\Exception\AccessException
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'left_symbol'       => '',
                'right_symbol'      => '',
                'symbol'            => '',
                'inputmask_pattern' => '',
                'compound'          => false,
                'transformToFloat'  => true
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['transformToFloat']) {
            $builder->addModelTransformer(
                new CallbackTransformer(
                    function ($originalValue) {
                        return $originalValue;
                    },
                    function ($submittedValue) use ($options) {
                        if (isset($options['inputmask_pattern']['groupSeparator'])) {
                            $submittedValue = str_replace($options['inputmask_pattern']['groupSeparator'], '', $submittedValue);
                        }

                        if (isset($options['inputmask_pattern']['radixPoint'])) {
                            $submittedValue = str_replace($options['inputmask_pattern']['radixPoint'], '.', $submittedValue);
                        }

                        return $submittedValue;
                    }
                )
            );
        }
    }

    /**
     * @param FormView      $view
     * @param FormInterface $form
     * @param array         $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if ($options['inputmask_pattern']) {
            $inputmaskPattern = !is_array($options['inputmask_pattern']) ? ['mask' => $options['inputmask_pattern']] : $options['inputmask_pattern'];

            $view->vars = array_replace(
                $view->vars,
                [
                    'attr' => array_replace(
                        $view->vars['attr'],
                        [
                            'v-xlite-pattern'   => '',
                            'inputmask-pattern' => json_encode($inputmaskPattern),
                        ]
                    ),
                ]
            );
        }

        $view->vars = array_replace(
            $view->vars,
            [
                'left_symbol'  => $options['left_symbol'] ?: $options['symbol'],
                'right_symbol' => $options['right_symbol'],
            ]
        );
    }
}
