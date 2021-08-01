<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Geolocation\Core;

use Includes\Utils\ArrayManager;

/**
 * Current session
 */
class Session extends \XLite\Core\Session implements \XLite\Base\IDecorator
{
    /**
     * Define current language
     *
     * @return string Language code
     */
    protected function defineCurrentLanguage()
    {
        $languages = \XLite\Core\Database::getRepo('XLite\Model\Language')->findActiveLanguages();
        if (!\XLite::isAdminZone() && !empty($languages)) {
            $data = \XLite\Module\XC\Geolocation\Logic\Geolocation::getInstance()->getLocation(new \XLite\Module\XC\Geolocation\Logic\GeoInput\IpAddress());

            if (isset($data['country'])) {
                $country = \XLite\Core\Database::getRepo('XLite\Model\Country')->find($data['country']);

                if ($country &&
                    $country->getLanguage()
                ) {
                    $result = ArrayManager::searchInObjectsArray(
                        $languages,
                        'getCode',
                        $country->getLanguage()->getCode(),
                        true
                    );
                }
            }
        }

        return isset($result)
            ? $result->getCode()
            : parent::defineCurrentLanguage();
    }
}
