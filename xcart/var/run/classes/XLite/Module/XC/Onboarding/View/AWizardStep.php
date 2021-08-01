<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Onboarding\View;

use XLite\Core\Converter;

/**
 * FacebookMarketing
 */
abstract class AWizardStep extends \XLite\View\AView
{
    /**
     * @return string
     */
    protected function getStepIndex()
    {
        $fqcn = get_called_class();
        $class = explode('\\', $fqcn);
        $index = end($class);

        return Converter::convertFromCamelCase($index);
    }

    /**
     * Return Shop URL
     *
     * @return string
     */
    public function getShopURL()
    {
        return \XLite::getInstance()->getShopURL();
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/XC/Onboarding/wizard_steps/' . $this->getStepIndex();
    }

    protected function getDefaultTemplate()
    {
        return $this->getDir() . "/body.twig";
    }

    public function getCSSFiles()
    {
        return array_merge(parent::getCSSFiles(), [
            $this->getDir() . '/style.less'
        ]);
    }

    public function getJSFiles()
    {
        return array_merge(parent::getJSFiles(), [
            $this->getDir() . '/step.js',
        ]);
    }
}