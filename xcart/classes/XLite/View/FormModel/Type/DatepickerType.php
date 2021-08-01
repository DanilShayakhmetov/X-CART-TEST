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
use XLite\View\FormModel\Type\Base\AType;

class DatepickerType extends AType
{
    /**
     * Register files from common repository
     *
     * @return array
     */
    public static function getCommonFiles()
    {
        return [
            \XLite\View\AView::RESOURCE_JS => ['js/jquery-ui-i18n.min.js'],
        ];
    }

    /**
     * @return array
     */
    public static function getJSFiles()
    {
        return ['form_model/type/datepicker_type.js'];
    }

    public static function getCSSFiles()
    {
        return ['form_field/input/text/date.less'];
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return 'Symfony\Component\Form\Extension\Core\Type\TextType';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(
            new CallbackTransformer(
                function ($originalValue) {
                    $dateTime = new \DateTime('now', \XLite\Core\Converter::getTimeZone());
                    $dateTime->setTimestamp($originalValue);

                    $formats = \XLite\Core\Converter::getDateFormatsByStrftimeFormat();
                    $format = $formats['phpFormat'];

                    return $dateTime->format($format);
                },
                function ($submittedValue) {
                    return \XLite\Core\Converter::parseFromJsFormat($submittedValue);
                }
            )
        );
    }

    /**
     * @return int
     */
    protected function getStartDay()
    {
        $start = \XLite\Core\Config::getInstance()->Units->week_start;

        $starts = [
            'sun' => 0,
            'mon' => 1,
            'tue' => 2,
            'wed' => 3,
            'thu' => 4,
            'fri' => 5,
            'sat' => 6,
        ];

        return isset($starts[$start])
            ? $starts[$start]
            : 0;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $currentFormats = \XLite\Core\Converter::getDateFormatsByStrftimeFormat();

        $resolver->setDefaults(
            [
                'date_format'   => $currentFormats['jsFormat'],
                'first_day'     => $this->getStartDay(),
                'locale'        => $this->getLocaleCode(\XLite\Core\Session::getInstance()->getLanguage()->getCode()),
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
        $view->vars = array_replace($view->vars, [
            'v_model' => '',
            'attr'    => array_replace(
                $view->vars['attr'],
                [
                    'v-datepicker'  => $view->vars['v_model'],
                    'detect-change' => 'off',
                    'detect-blur'   => 'off',
                    'format'        => $options['date_format'],
                    'firstday'      => $options['first_day'],
                    'locale'        => $options['locale'],
                ]
            )
        ]);
    }

    /**
     * @param string $language
     *
     * @return string
     */
    protected function getLocaleCode($language)
    {
        $locales = array(
            'zh_CN',
        );

        $locale = array_filter($locales, function ($item) use ($language) {
            return strpos($item, strtolower($language)) === 0;
        });

        return 1 === count($locale) ? reset($locale) : $language;
    }
}
