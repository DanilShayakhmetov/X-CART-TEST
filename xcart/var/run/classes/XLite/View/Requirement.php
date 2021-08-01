<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

use Includes\Requirements;
use XLite\Core\Cache\ExecuteCached;

/**
 * Requirements
 */
class Requirement extends \XLite\View\AView
{
    const PARAM_REQUIREMENT = 'requirement';
    const PARAM_NAME = 'name';
    const PARAM_ODD = 'odd';

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            static::PARAM_REQUIREMENT => new \XLite\Model\WidgetParam\TypeCollection('Requirement', array()),
            static::PARAM_NAME        => new \XLite\Model\WidgetParam\TypeString('Name', array()),
            static::PARAM_ODD         => new \XLite\Model\WidgetParam\TypeBool('Odd', false),
        );
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'settings/requirement/style.less';

        return $list;
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'settings/requirement/controller.js';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'settings/requirement/body.twig';
    }

    public function getRequirement()
    {
        $requirement = $this->getParam(static::PARAM_REQUIREMENT);

        $name = $this->getName();
        if (!$requirement && $name) {
            $requirement = $this->processRequirement(
                $this->getRequirementResult($name, true),
                $name
            );

            ExecuteCached::setCache(['Requirement', $name], $requirement);
        }

        return $requirement;
    }

    public function getRequirementResult($name, $force = false)
    {
        return ExecuteCached::executeCached(static function () use ($name) {
            $requirements = new Requirements();

            return $requirements->getSingleResult($name);
        }, ['requirement', $name], 0, $force);
    }

    public function getName()
    {
        return $this->getParam(static::PARAM_NAME)
            ?: \XLite\Core\Request::getInstance()->name;
    }

    public function isOdd()
    {
        return $this->getParam(static::PARAM_ODD)
            ?: \XLite\Core\Request::getInstance()->odd;
    }

    public function isAjax()
    {
        return \XLite\Core\Request::getInstance()->isAJAX();
    }

    /**
     * @param $requirement
     *
     * @return mixed
     */
    protected function processRequirement($requirement, $name)
    {
        $requirement['status'] = $requirement['state'] === \Includes\Requirements::STATE_SUCCESS;
        $requirement['title'] = static::t($requirement['title']);
        $requirement['skipped'] = false;

        $requirement['error_description'] = isset($requirement['description'])
            ? $this->getRequirementTranslation($name . '.' . $requirement['description'], $requirement)
            : '';

        $requirement['description'] = $this->getRequirementTranslation($name . '.label_message', $requirement);
        $requirement['kb_description'] = $this->getRequirementTranslation($name . '.kb_message', $requirement);

        return $requirement;
    }

    protected function getRequirementTranslation($name, $requirement)
    {
        $value = static::t($name, array_filter($requirement['data'], 'is_scalar'));

        return $name === $value->translate() ? '' : $value;
    }
}
