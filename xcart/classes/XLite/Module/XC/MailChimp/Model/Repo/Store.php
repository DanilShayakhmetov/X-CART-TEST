<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\Model\Repo;

/**
 * @Api\Operation\Read(modelClass="XLite\Module\XC\MailChimp\Model\Store", summary="Retrieve mailchimp stores by id")
 * @Api\Operation\ReadAll(modelClass="XLite\Module\XC\MailChimp\Model\Store", summary="Retrieve mailchimp stores by conditions")
 *
 * @SWG\Tag(
 *   name="XC\MailChimp\Store",
 *   x={"display-name": "Store", "group": "XC\MailChimp"},
 *   description="Store repo",
 * )
 */
class Store extends \XLite\Model\Repo\ARepo
{
}
