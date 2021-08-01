<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\CloudSearch\Controller\Admin;


use XLite\Module\QSL\CloudSearch\Controller\ApiControllerTrait;

/**
 * CloudSearch API controller for X-Cart Cloud stores
 */
class CloudSearchApi extends \XLite\Controller\Admin\AAdmin
{
    use ApiControllerTrait;

    /**
     * Check - is current place public or not
     *
     * @return boolean
     */
    protected function isPublicZone()
    {
        return true;
    }

    /**
     * Check if the form ID validation is needed
     *
     * @return boolean
     */
    protected function isActionNeedFormId()
    {
        return false;
    }

    /**
     * Preprocessor for no-action run
     *
     * @return void
     */
    protected function doNoAction()
    {
        $this->markAsAccessDenied();
    }
}