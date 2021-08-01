<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormModel\Type;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use XLite\View\AView;
use XLite\View\FormModel\Type\Base\AType;

class PatternType extends AType
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
        return ['form_model/type/pattern_type.js'];
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return 'Symfony\Component\Form\Extension\Core\Type\TextType';
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
                'inputmask_pattern' => '',
                'compound'          => false
            ]
        );
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
    }
}
