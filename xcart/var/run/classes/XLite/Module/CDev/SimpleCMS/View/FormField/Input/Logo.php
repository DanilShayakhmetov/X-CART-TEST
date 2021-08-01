<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\SimpleCMS\View\FormField\Input;

use XLite\Core\Database;
use XLite\Core\Skin;

/**
 * Logo
 */
class Logo extends \XLite\Module\CDev\SimpleCMS\View\FormField\Input\AImage
{
    /**
     * @return boolean
     */
    protected function hasAlt()
    {
        return true;
    }

    /**
     * @return string
     */
    protected function getFieldLabelTemplate()
    {
        return 'form_field/label/logo_label.twig';
    }

    /**
     * Set widget params
     *
     * @param array $params Handler params
     *
     * @return void
     */
    public function setWidgetParams(array $params)
    {
        parent::setWidgetParams($params);

        $logoSettings= Database::getRepo(\XLite\Model\ImageSettings::class)->findOneByRecord(
            [
                'code' => 'Default',
                'model' => 'XLite\Model\Image\Common\Logo',
                'moduleName' => Skin::getInstance()->getCurrentSkinModuleId()
            ]
        );

        if ($logoSettings) {
            $this->widgetParams[static::PARAM_HELP]->setValue(static::t(
                'Current logo sizes: XхY px',
                [
                    'X' => $logoSettings->getWidth(),
                    'Y' => $logoSettings->getHeight()
                ]
            ));
        }
    }

    /**
     * @return boolean
     */
    protected function isViaUrlAllowed() {
        return false;
    }
}