<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Coupons\Model\Repo;

/**
 * @Api\Operation\Create(modelClass="XLite\Module\CDev\Coupons\Model\UsedCoupon", summary="Add used coupon")
 * @Api\Operation\Read(modelClass="XLite\Module\CDev\Coupons\Model\UsedCoupon", summary="Retrieve used coupon by id")
 * @Api\Operation\ReadAll(modelClass="XLite\Module\CDev\Coupons\Model\UsedCoupon", summary="Retrieve used coupons by conditions")
 * @Api\Operation\Update(modelClass="XLite\Module\CDev\Coupons\Model\UsedCoupon", summary="Update used coupon by id")
 * @Api\Operation\Delete(modelClass="XLite\Module\CDev\Coupons\Model\UsedCoupon", summary="Delete used coupon by id")
 *
 * @SWG\Tag(
 *   name="CDev\Coupons\UsedCoupon",
 *   x={"display-name": "UsedCoupon", "group": "CDev\Coupons"},
 *   description="UsedCoupon represents the coupon which has already been applied to the specific order.",
 *   @SWG\ExternalDocumentation(
 *     description="Find out more about discount coupons",
 *     url="https://kb.x-cart.com/seo_and_promotion/setting_up_discount_coupons.html"
 *   )
 * )
 */
class UsedCoupon extends \XLite\Model\Repo\ARepo
{
}
