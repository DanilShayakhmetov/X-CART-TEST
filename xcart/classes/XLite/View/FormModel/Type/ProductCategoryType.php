<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormModel\Type;

use Symfony\Component\Form\ChoiceList\View\ChoiceView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use XLite\View\AView;
use XLite\View\FormModel\Type\Base\AType;

/**
 * Class used for Categories type on product modify page.
 *
 * It lazy load there choices, so list populated only with selected values
 */
class ProductCategoryType extends AType
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
        return ['form_model/type/product_category_type.js'];
    }

    /**
     * @return array
     */
    public static function getCSSFiles()
    {
        return ['form_model/type/product_category_type.css'];
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return 'Symfony\Component\Form\Extension\Core\Type\ChoiceType';
    }

    /**
     * @param OptionsResolver $resolver
     *
     * @throws \Symfony\Component\OptionsResolver\Exception\AccessException
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $loader = new ProductCategoryLoader();

        $resolver->setDefaults(
            [
                'choice_loader' => $loader,
                'choice_label'  => function ($value) use ($loader) {
                    /**
                     * When $loader::getValueLabel() called $loader::loadValuesForChoices() already invoked
                     */
                    return $loader->getValueLabel($value);
                },
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
        $fullNames = [];
        $accessible  = [];
        $view->vars['choices'] = [];

        foreach ($view->vars['value'] as $selectedCategoryId) {
            $selectedCategory = \XLite\Core\Database::getRepo('\XLite\Model\Category')->getCategory($selectedCategoryId);
            $fullNames[$selectedCategoryId] = $selectedCategory->getStringPath();
            if (!$selectedCategory->isVisible()) {
                $accessible[] = $selectedCategoryId;
            }

            $view->vars['choices'][] = new ChoiceView($selectedCategoryId, $selectedCategoryId,
                $selectedCategory->getStringPath());
        }

        list($namesSorted, $choicesSorted) = $this->getSortedNamesAndChoices(
            $fullNames,
            $view->vars['choices'],
            $view->vars['value'],
            $accessible
        );

        $view->vars = array_replace($view->vars, [
            'attr' => array_replace(
                $view->vars['attr'],
                [
                    'v-xlite-product-category' => $view->vars['v_model'],
                    'searching-lbl'            => static::t('Searching...'),
                    'no-results-lbl'           => static::t('No results found.'),
                    'enter-term-lbl'           => static::t('Enter a keyword to search.'),
                    'placeholder-lbl'          => static::t('No category assigned'),
                    'disabled-lbl'             => static::t('Category is not accessible'),
                    'data-categories'          => json_encode($namesSorted),
                    'short-lbl'                => static::t('Please enter 3 or more characters'),
                    'more-lbl'                 => static::t('Loading more results...'),
                ]
            ),
            'choices' => $choicesSorted,
        ]);
    }

    /**
     * @param array $names
     * @param array $choices
     * @param array $selectedCategories
     * @param array $disabledCategories
     *
     * @return array
     */
    protected function getSortedNamesAndChoices($names, $choices, $selectedCategories, $disabledCategories)
    {
        $namesSorted = [];
        $choicesSorted = [];

        $choicesIds = array_map(function($choice) {
            /** @var ChoiceView $choice */
            return $choice->data;
        }, $choices);
        $choices = array_combine($choicesIds, $choices);

        foreach ($selectedCategories as $id) {
            $namesSorted[$id] = $names[$id];
            $choicesSorted[$id] = $choices[$id];
        }

        foreach ($disabledCategories as $id) {
            $choices[$id]->attr['data-disabled'] = 'true';
        }

        $namesSorted = array_replace(
            $namesSorted,
            $names
        );

        $choicesSorted = array_replace(
            $choicesSorted,
            $choices
        );

        return [
            $namesSorted,
            $choicesSorted
        ];
    }
}
