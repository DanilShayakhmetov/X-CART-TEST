<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\Core;

use XLite\Core\Cache\ExecuteCachedTrait;
use XLite\Module\XC\ThemeTweaker\Model\Template;

/**
 * Request
 */
class TemplateObjectProvider extends \XLite\Base\Singleton
{
    use ExecuteCachedTrait;

    /**
     * Return current model ID
     *
     * @return integer
     */
    public function getTemplateId()
    {
        return \XLite\Core\Request::getInstance()->templateId;
    }

    /**
     * Return current model filepath
     *
     * @return string
     */
    public function getTemplatePath()
    {
        return \XLite\Core\Request::getInstance()->templatePath;
    }

    /**
     * Template Object
     *
     * @return Template
     */
    public function getTemplateObject()
    {
        return $this->executeCachedRuntime(function(){
            $model = $this->getTemplateId()
                ? \XLite\Core\Database::getRepo('XLite\Module\XC\ThemeTweaker\Model\Template')->find($this->getTemplateId())
                : null;

            if (!$model && $this->getTemplatePath()) {
                $localPath = $this->getTemplatePath();
                $model = \XLite\Core\Database::getRepo('XLite\Module\XC\ThemeTweaker\Model\Template')
                    ->findOneByTemplate($localPath);
            }

            return $model ?: new Template();
        }, [$this->getTemplateId(), $this->getTemplatePath()]);
    }
}
