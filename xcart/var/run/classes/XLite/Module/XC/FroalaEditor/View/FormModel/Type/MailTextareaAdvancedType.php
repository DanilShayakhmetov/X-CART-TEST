<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\FroalaEditor\View\FormModel\Type;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

 class MailTextareaAdvancedType extends \XLite\View\FormModel\Type\MailTextareaAdvancedTypeAbstract implements \XLite\Base\IDecorator
{
    /**
     * @return string
     */
    public function getParent()
    {
        return $this->useAsEmailEidtor()
            ? 'XLite\View\FormModel\Type\OldType'
            : parent::getParent();
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        if ($this->useAsEmailEidtor()) {
            $resolver->setDefaults([
                'oldType' => 'XLite\Module\XC\FroalaEditor\View\FormField\Textarea\MailAdvanced',
            ]);

        } else {
            parent::configureOptions($resolver);
        }
    }

    /**
     * @param FormView      $view
     * @param FormInterface $form
     * @param array         $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if ($this->useAsEmailEidtor()) {
            $view->vars = array_replace($view->vars, [
                'input_grid'   => 'froala-widget-column',
                'fieldOptions' => array_replace(
                    $view->vars['fieldOptions'],
                    [
                        'attributes' => [
                            'v-model' => $view->vars['v_model'],
                            'id'      => '',
                        ],
                        'value'      => $view->vars['value'],
                    ]
                ),
            ]);

        } else {
            parent::buildView($view, $form, $options);
        }
    }

    /**
     * @return boolean
     */
    protected function useAsEmailEidtor()
    {
        return !empty(\XLite\Core\Config::getInstance()->XC->FroalaEditor->use_as_email_editor);
    }
}
