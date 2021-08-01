<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormModel\Type\Base;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use XLite\View\AView;

abstract class AutocompleteType extends AType
{
    /**
     * @return array
     */
    public static function getCommonFiles()
    {
        return [
            AView::RESOURCE_JS  => ['select2/dist/js/select2.js', 'select2_plugins/select2.sortable.js'],
            AView::RESOURCE_CSS => ['select2/dist/css/select2.min.css'],
        ];
    }

    /**
     * @return array
     */
    public static function getJSFiles()
    {
        return ['form_model/type/autocomplete_type.js'];
    }
    
    /**
     * @return string
     */
    public function getParent()
    {
        return 'Symfony\Component\Form\Extension\Core\Type\ChoiceType';
    }

    /**
     * @return bool|string
     */
    protected function getPlaceholder()
    {
        return false;
    }

    /**
     * @return string
     */
    abstract protected function getDictionary();

    /**
     * @param $selectedValue
     *
     * @return array
     */
    abstract protected function getSelectedChoices($selectedValue);

    /**
     * @param FormView      $view
     * @param FormInterface $form
     * @param array         $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['choices'] = [];

        $selectedChoices = $this->getSelectedChoices($view->vars['value']);

        $view->vars = array_replace($view->vars, [
            'attr' => array_replace(
                $view->vars['attr'],
                [
                    'v-xlite-autocomplete'     => $view->vars['v_model'],
                    'searching-lbl'            => static::t('Searching...'),
                    'no-results-lbl'           => static::t('No results found.'),
                    'enter-term-lbl'           => static::t('Enter a keyword to search.'),
                    'placeholder-lbl'          => $this->getPlaceholder(),
                    'short-lbl'                => static::t('Please enter 3 or more characters'),
                    'more-lbl'                 => static::t('Loading more results...'),
                    'dictionary'               => $this->getDictionary(),
                ]
            ),
            'choices' => $selectedChoices,
        ]);
    }
}
