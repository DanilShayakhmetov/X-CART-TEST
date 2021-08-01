<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormModel\Type;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use XLite\View\FormModel\Type\Base\AType;

/**
 * Class UploaderType
 */
class UploaderType extends AType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'compound'      => false,
                'uploaderClass' => '\XLite\View\FormField\FileUploader\Image',
                'imageClass'    => '\XLite\Model\TemporaryFile',
                'files'         => null,
                'multiple'      => false,
                'helpMessage'   => null,
                'previewWidth'  => null,
                'previewHeight' => null
            ]
        );
    }

    /**
     * @param FormView $view
     * @param FormInterface $form
     * @param array $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $varsToPopulate = [
            'options'   => [
                'value'     => $options['files'],
                'multiple'  => $options['multiple'],
                'fieldOnly' => true,
                'fieldName' => $view->vars['full_name'],
                'helpMessage'   => $options['helpMessage'],
            ],
            'uploaderClass'     => $options['uploaderClass'],
        ];

        if ($options['previewHeight']) {
            $varsToPopulate['options']['maxHeight'] = $options['previewHeight'];
        }

        if ($options['previewWidth']) {
            $varsToPopulate['options']['maxWidth'] = $options['previewWidth'];
        }

        $view->vars = array_replace(
            $view->vars,
            $varsToPopulate
        );
    }

    protected function prepareValue($viewData, $class)
    {
        if ($viewData) {
            if (is_array($viewData)) {
                $result = [];
                foreach ($viewData as $k => $v) {
                    if (!is_array($v)) {
                        continue;
                    }

                    $file = isset($v['temp_id']) && $v['temp_id']
                        ? \XLite\Core\Database::getRepo('\XLite\Model\TemporaryFile')->find($v['temp_id'])
                        : \XLite\Core\Database::getRepo($class)->find($k);

                    if ($file) {
                        $result[$k] = $file;
                    }
                }

                $viewData = $result;
            }
        }

        return $viewData;
    }
}
