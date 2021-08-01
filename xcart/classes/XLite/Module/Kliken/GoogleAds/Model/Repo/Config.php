<?php

namespace XLite\Module\Kliken\GoogleAds\Model\Repo;

/**
 * The Product model repository extension
 */
class Config extends \XLite\Model\Repo\Config implements \XLite\Base\IDecorator
{
    protected function prepareCndName(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if ($value) {
            $queryBuilder->andWhere('c.name = :name')
                ->setParameter('name', $value);
        }
    }

    protected function prepareCndCategory(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if ($value) {
            $queryBuilder->andWhere('c.category = :category')
                ->setParameter('category', $value);
        }
    }
}
