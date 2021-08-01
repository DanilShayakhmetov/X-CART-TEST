<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\Core\Templating\Twig;


use XLite\Core\Exception\CallToAMethodOnNonObject;
use XLite\Core\Exception\MethodNotFound;
use XLite\Module\XC\ThemeTweaker\Controller\Admin\NotificationEditor;

abstract class Template extends \XLite\Core\Templating\Twig\Template implements \XLite\Base\IDecorator
{
    protected function logMethodNotFoundInTemplate(MethodNotFound $e, $object)
    {
        if (!$this->isNotificationEditor()) {
            parent::logMethodNotFoundInTemplate($e, $object);
        } else {
            $this->setTemplateError();
        }
    }

    protected function logMethodTypeErrorInTemplate(\TypeError $e)
    {
        if (!$this->isNotificationEditor()) {
            parent::logMethodTypeErrorInTemplate($e);
        } else {
            $this->setTemplateError();
        }
    }

    protected function logCallToMethodOnNonObjectInTemplate(CallToAMethodOnNonObject $e)
    {
        if (!$this->isNotificationEditor()) {
            parent::logCallToMethodOnNonObjectInTemplate($e);
        } else {
            $this->setTemplateError();
        }
    }

    /**
     * @return bool
     */
    protected function isNotificationEditor()
    {
        return \XLite::getController() instanceof NotificationEditor;
    }

    /**
     * @see \XLite\Module\XC\ThemeTweaker\Controller\Admin\NotificationEditor::addFailedTemplate
     */
    protected function setTemplateError()
    {
        \XLite::getController()->addFailedTemplate(
            $this->getSourceContext()->getPath()
        );
    }

    /**
     * @return bool
     */
    protected function isThemeTweakerTemplate()
    {
        return mb_strpos(
                $this->getSourceContext()->getPath(),
                LC_DIR_SKINS . 'theme_tweaker' . LC_DS
            ) === 0;
    }
}