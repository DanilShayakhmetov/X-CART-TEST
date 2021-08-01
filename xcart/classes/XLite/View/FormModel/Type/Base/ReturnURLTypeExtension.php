<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormModel\Type\Base;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReturnURLTypeExtension extends AbstractTypeExtension
{
    /**
     * Adds a CSRF field to the root form view.
     *
     * @param FormView      $view    The form view
     * @param FormInterface $form    The form
     * @param array         $options The options
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        if ($options['return_url'] && !$view->parent && $options['compound']) {
            $factory = $form->getConfig()->getFormFactory();

            $csrfForm = $factory->createNamed(
                $options['return_url_field_name'],
                'XLite\View\FormModel\Type\Base\SystemHiddenType',
                (string) $options['return_url'],
                [
                    'mapped' => false,
                ]
            );

            $view->children[$options['return_url_field_name']] = $csrfForm->createView($view);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'return_url' => '',
            'return_url_field_name' => \XLite\Controller\AController::RETURN_URL,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public static function getExtendedTypes(): iterable
    {
        return [\Symfony\Component\Form\Extension\Core\Type\FormType::class];
    }
}
