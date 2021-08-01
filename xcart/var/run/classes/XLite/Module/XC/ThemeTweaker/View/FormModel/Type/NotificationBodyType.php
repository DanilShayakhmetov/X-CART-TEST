<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\View\FormModel\Type;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use XLite\Module\XC\ThemeTweaker\Core\Notifications\Data;
use XLite\Module\XC\ThemeTweaker\Core\Notifications\ErrorTranslator;
use XLite\View\FormModel\Type\Base\AType;

class NotificationBodyType extends AType
{
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
                'url' => '',
            ]
        );
    }

    /**
     * @return Data
     */
    protected function getDataSource()
    {
        return \XLite::getController()->getDataSource();
    }

    /**
     * @param FormView      $view
     * @param FormInterface $form
     * @param array         $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $unavailabilityReason = '';

        if (!$this->getDataSource()->isAvailable()) {
            foreach ($this->getDataSource()->getUnavailableProviders() as $provider) {
                $unavailabilityReason = ErrorTranslator::translateAvailabilityError($provider) ?: '';

                if ($unavailabilityReason) {
                    break;
                }
            }
        }

        $view->vars = array_replace(
            $view->vars,
            [
                'url'                  => $options['url'],
                'editable'             => $this->getDataSource()->isEditable(),
                'available'            => $this->getDataSource()->isAvailable(),
                'unavailabilityReason' => $unavailabilityReason,
            ]
        );
    }
}
