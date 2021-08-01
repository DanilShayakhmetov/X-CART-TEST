<?php

namespace XLite\Module\Kliken\GoogleAds\Model\Repo;

/**
 * The Product model repository extension
 */
 class Product extends \XLite\Module\QSL\CloudSearch\Model\Repo\Product implements \XLite\Base\IDecorator
{
    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array                      $value        Condition data
     */
    protected function prepareCndCreateDate(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        $this->assignDateRangeCondition($queryBuilder, $value, 'date');
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array                      $value        Condition data
     */
    protected function prepareCndUpdateDate(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        $this->assignDateRangeCondition($queryBuilder, $value, 'updateDate');
    }

    /**
     * Assign date range-based search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder
     * @param array                      $value        Condition data
     * @param string                     $dateField    Date field in the database
     */
    private function assignDateRangeCondition(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $dateField)
    {
        if (is_array($value) && 2 === count($value)) {
            list($min, $max) = $value;

            if ($min) {
                $queryBuilder->andWhere('p.' . $dateField . ' >= :minDate')
                    ->setParameter('minDate', $min);
            }

            if ($max) {
                $queryBuilder->andWhere('p.' . $dateField . ' <= :maxDate')
                    ->setParameter('maxDate', $max);
            }
        }
    }

    /**
     * Prepare certain search condition
     * Accept a comma-separated list of product ids to query against
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param string                     $value        Condition data
     */
    protected function prepareCndProductIds(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if (!is_array($value)) {
            $value = array_map(function($item) {
                return intval(trim($item));
            }, explode(',', $value));
        }

        if ($value !== null && count($value) > 0) {
            $queryBuilder->andWhere($queryBuilder->expr()->in('p.product_id', $value));
        }
    }
}
