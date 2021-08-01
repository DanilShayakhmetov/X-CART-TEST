<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\Controller\Admin;

use XLite\Core\Translation;

/**
 * Language labels controller
 */
class Labels extends \XLite\Controller\Admin\Labels implements \XLite\Base\IDecorator
{
    /**
     * Define the actions with no secure token
     *
     * @return array
     */
    public static function defineFreeFormIdActions()
    {
        return array_merge(parent::defineFreeFormIdActions(), array('searchItemsList', 'edit'));
    }

    /**
     * Is called when doActionEdit() has been performed successfully; sends the appropriate message to the user
     *
     * @param \XLite\Model\LanguageLabel $lbl Edited label entity
     */
    protected function onEditSuccess($lbl)
    {
        $requestData = \XLite\Core\Request::getInstance()->getNonFilteredData();
        $substitutions = isset($requestData['substitutions']) ? $requestData['substitutions'] : array();
        $code = isset($requestData['code']) ? $requestData['code'] : null;

        \XLite\Core\Event::editedLabel([
            'name' => $lbl->getName(),
            'translation' => (string) Translation::getInstance()->translateAsEditable($lbl->getName(), $substitutions, $code),
        ]);

        parent::onEditSuccess($lbl);
    }
}
