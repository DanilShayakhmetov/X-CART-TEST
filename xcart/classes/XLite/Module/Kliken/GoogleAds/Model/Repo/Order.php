<?php

namespace XLite\Module\Kliken\GoogleAds\Model\Repo;

/**
 * The Product model repository extension
 */
class Order extends \XLite\Model\Repo\Order implements \XLite\Base\IDecorator
{
    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array                      $value        Condition data
     */
    protected function prepareCndUpdateDate(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if (is_array($value) && 2 === count($value)) {
            list($min, $max) = $value;

            if ($min) {
                $queryBuilder->andWhere('o.lastRenewDate >= :minDate')
                    ->setParameter('minDate', $min);
            }

            if ($max) {
                $queryBuilder->andWhere('o.lastRenewDate <= :maxDate')
                    ->setParameter('maxDate', $max);
            }
        }
    }
}
