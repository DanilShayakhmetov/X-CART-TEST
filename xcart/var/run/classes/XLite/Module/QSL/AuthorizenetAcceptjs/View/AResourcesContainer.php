<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\AuthorizenetAcceptjs\View;

/**
 * Resources container routine
 */
abstract class AResourcesContainer extends \XLite\View\AResourcesContainerAbstract implements \XLite\Base\IDecorator
{
    /**
     * @inheritdoc
     */
    protected function getResourceURL($url, $params = [])
    {
        return preg_match('/\/Accept.js$/iSs', $url)
            ? $url
            : parent::getResourceURL($url, $params);
    }

}
