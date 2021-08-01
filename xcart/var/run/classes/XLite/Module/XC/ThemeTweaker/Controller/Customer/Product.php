<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\Controller\Customer;

use XLite\Core\Request;
use XLite\Module\XC\ThemeTweaker\Core\ThemeTweaker;

/**
 * ThemeTweaker controller
 */
 class Product extends \XLite\Controller\Customer\ProductAbstract implements \XLite\Base\IDecorator
{
    /**
     * Process an action 'preview'
     *
     * @return void
     */
    public function doActionPreview()
    {
        if (Request::getInstance()->activate_editor) {
            ThemeTweaker::getInstance()->setCurrentMode(ThemeTweaker::MODE_INLINE_EDITOR);

            $this->setReturnURL($this->getURL(['action' => 'preview']));
        }

        parent::doActionPreview();
    }

    /**
     * Get controller parameters
     * TODO - check this method
     * FIXME - backward compatibility
     *
     * @param string $exceptions Parameter keys string OPTIONAL
     *
     * @return array
     */
    public function getAllParams($exceptions = null)
    {
        $params = parent::getAllParams($exceptions);

        if (Request::getInstance()->activate_editor
            && ThemeTweaker::getInstance()->getCurrentMode() === ThemeTweaker::MODE_INLINE_EDITOR) {
            unset($params['activate_editor']);
        }

        return $params;
    }
}
