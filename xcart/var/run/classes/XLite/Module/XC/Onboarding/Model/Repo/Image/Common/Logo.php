<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Onboarding\Model\Repo\Image\Common;

use XLite\Core\Request;
use XLite\Module\XC\Onboarding\Core\WizardState;
use Includes\Utils\FileManager;
use XLite\Module\XC\Onboarding\View\WizardStep\CompanyLogoAdded;

 class Logo extends \XLite\Model\Repo\Image\Common\LogoAbstract implements \XLite\Base\IDecorator
{
    /**
     * @return \XLite\Model\Image\Common\Logo
     */
    public function getLogo()
    {
        $cookieLogoURL = Request::getInstance()->{WizardState::COOKIE_LASTLOGO};
        if (strpos($cookieLogoURL, '//') === 0) {
            $cookieLogoURL = str_replace('//', 'http://', $cookieLogoURL);
        }

        if (Request::getInstance()->{CompanyLogoAdded::CHECK_NEW_LOGO} && $cookieLogoURL) {
            $cookieLogo = new \XLite\Model\Image\Content();
            $cookieLogo->loadFromURL($cookieLogoURL, true);
            $cookieLogoRelativePath = FileManager::getRelativePath(
                $cookieLogo->getStoragePath(),
                LC_DIR_ROOT
            );

            return self::getFakeImageObject($cookieLogoRelativePath);
        }

        return parent::getLogo();
    }
}
