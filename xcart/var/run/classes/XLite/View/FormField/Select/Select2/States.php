<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace XLite\View\FormField\Select\Select2;

use XLite\View\FormField\Select\MultipleTrait;

class States extends \XLite\View\FormField\Select\Multiple
{
    /**
     * Register files from common repository
     *
     * @return array
     */
    public function getCommonFiles()
    {
        $list = parent::getCommonFiles();
        $list[static::RESOURCE_JS][] = 'select2/dist/js/select2.min.js';
        $list[static::RESOURCE_CSS][] = 'select2/dist/css/select2.min.css';

        return $list;
    }

    /**
     * @inheritdoc
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'form_field/select/select2/states.js';

        return $list;
    }

    /**
     * @inheritdoc
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'form_field/input/text/autocomplete.css';

        return $list;
    }

    /**
     * Get value container class
     *
     * @return string
     */
    protected function getValueContainerClass()
    {
        return parent::getValueContainerClass() . ' input-states-select2';
    }

    public function setValue($value)
    {
        if (is_object($value) && $value instanceOf \Doctrine\Common\Collections\Collection) {
            $value = $value->toArray();

        } elseif (!is_array($value)) {
            $value = array($value);
        }

        foreach ($value as $k => $v) {
            if (is_object($v) && $v instanceOf \XLite\Model\AEntity) {
                $value[$k] = $v->getCountry()->getCode() . '_' . $v->getCode();
            }
        }

        parent::setValue($value);
    }

    /**
     * Get selector default options list
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        /** @var \XLite\Model\State[] $list */
        $list = \XLite\Core\Database::getRepo('XLite\Model\State')->findAllStates();

        $options = [];

        foreach ($list as $state) {
            $country = $state->getCountry();
            $code = $country->getCode() . '_' . $state->getCode();
            $countryName = $country->getCountry();
            $options[$code] = ($countryName ? "$countryName: " : '') . $state->getState();
        }

        return $options;
    }
}