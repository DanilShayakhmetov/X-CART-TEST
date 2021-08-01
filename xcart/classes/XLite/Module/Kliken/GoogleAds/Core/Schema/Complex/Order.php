<?php

namespace XLite\Module\Kliken\GoogleAds\Core\Schema\Complex;

use XLite\Module\Kliken\GoogleAds\Logic\Helper;

class Order extends \XLite\Module\XC\RESTAPI\Core\Schema\Complex\Order
{
    public function convertModel(\XLite\Model\AEntity $model, $withAssociations)
    {
        $return = parent::convertModel($model, $withAssociations);

        $return['coupons'] = [];

        // Check if CDev\Coupons module is active
        if (\Includes\Utils\ModulesManager::isActiveModule('CDev\Coupons')) {
            foreach ($model->getUsedCoupons() as $usedCoupon) {
                $cp = [
                    'id'     => $usedCoupon->getId(),
                    'name'   => $usedCoupon->getPublicName(),
                    'code'   => $usedCoupon->getPublicCode(),
                    'amount' => $usedCoupon->getCoupon()->getAmount($model),
                ];

                $return['coupons'][] = $cp;
            }
        }

        return $return;
    }
}
