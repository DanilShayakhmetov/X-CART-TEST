<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\NewsletterSubscriptions\Model\Repo;

/**
 * @Api\Operation\Create(modelClass="XLite\Module\XC\NewsletterSubscriptions\Model\Subscriber", summary="Add newsletter subscriber")
 * @Api\Operation\Read(modelClass="XLite\Module\XC\NewsletterSubscriptions\Model\Subscriber", summary="Retrieve newsletter subscriber by id")
 * @Api\Operation\ReadAll(modelClass="XLite\Module\XC\NewsletterSubscriptions\Model\Subscriber", summary="Retrieve newsletter subscribers by conditions")
 * @Api\Operation\Update(modelClass="XLite\Module\XC\NewsletterSubscriptions\Model\Subscriber", summary="Update newsletter subscriber by id")
 * @Api\Operation\Delete(modelClass="XLite\Module\XC\NewsletterSubscriptions\Model\Subscriber", summary="Delete newsletter subscriber by id")
 *
 * @SWG\Tag(
 *   name="XC\NewsletterSubscriptions\Subscriber",
 *   x={"display-name": "Subscriber", "group": "XC\NewsletterSubscriptions"},
 *   description="This repo stores all newsletter subscribers",
 * )
 */
class Subscriber extends \XLite\Model\Repo\ARepo
{
}
