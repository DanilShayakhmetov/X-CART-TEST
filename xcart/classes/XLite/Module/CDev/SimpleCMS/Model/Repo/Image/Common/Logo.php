<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\SimpleCMS\Model\Repo\Image\Common;


class Logo extends \XLite\Model\Repo\Image\Common\Logo implements \XLite\Base\IDecorator
{
    /**
     * @return \XLite\Model\Image\Common\Logo
     */
    public function getLogo()
    {
        $logo = parent::getLogo();

        if ($logo && $logo instanceof \XLite\Model\Image\Common\Logo) {
            $logo->setAlt(\XLite\Core\Config::getInstance()->CDev->SimpleCMS->logo_alt);
        }

        return $logo;
    }
}
