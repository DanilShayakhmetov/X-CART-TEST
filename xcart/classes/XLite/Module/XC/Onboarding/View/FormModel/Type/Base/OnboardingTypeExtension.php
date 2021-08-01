<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Onboarding\View\FormModel\Type\Base;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

class OnboardingTypeExtension extends AbstractTypeExtension
{
    /**
     * Adds an onboarding field to the root form view.
     *
     * @param FormView      $view    The form view
     * @param FormInterface $form    The form
     * @param array         $options The options
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $value = $this->getOnboardingExtensionValue();

        if (!$view->parent && $options['compound']) {
            $factory = $form->getConfig()->getFormFactory();
            $bulkEditForm = $factory->createNamed(
                'onboarding',
                'XLite\View\FormModel\Type\Base\SystemHiddenType',
                json_encode($value),
                [
                    'mapped' => false,
                ]
            );

            $view->children['onboarding'] = $bulkEditForm->createView($view);
        }
    }

    /**
     * @return mixed
     */
    public function getOnboardingExtensionValue()
    {
        $value = [];

        if (isset(\XLite\Core\Request::getInstance()->prefill)) {
            $value['prefilled_form'] = true;
        }

        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public static function getExtendedTypes(): iterable
    {
        return [\Symfony\Component\Form\Extension\Core\Type\FormType::class];
    }
}
