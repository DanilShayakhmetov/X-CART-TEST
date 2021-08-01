<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Reviews\View\Model;

/**
 * Module settings
 */
 class ModuleSettings extends \XLite\View\Model\ModuleSettingsAbstract implements \XLite\Base\IDecorator
{
    /**
     * Runtime cache
     *
     * @var boolean
     */
    protected $isReviewModule;

    /**
     * Return true if current module is XC\Reviews
     */
    protected function isReviewModule()
    {
        if (!isset($this->isReviewModule)) {
            $this->isReviewModule = $this->getModule() && 'XC-Reviews' === $this->getModule();
        }

        return $this->isReviewModule;
    }

    /**
     * Get form field by option
     *
     * @param \XLite\Model\Config $option Option
     *
     * @return array
     */
    protected function getFormFieldByOption(\XLite\Model\Config $option)
    {
        $cell = parent::getFormFieldByOption($option);

        if ($this->isReviewModule() && 'followupTimeout' == $option->getName()) {
            $cell[static::SCHEMA_DEPENDENCY] = array(
                static::DEPENDENCY_SHOW => array(
                    'enableCustomersFollowup' => array(true),
                ),
            );
        }

        return $cell;
    }
}
