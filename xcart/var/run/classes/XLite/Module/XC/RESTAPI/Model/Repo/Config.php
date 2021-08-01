<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\RESTAPI\Model\Repo;

/**
 * Category repository
 */
abstract class Config extends \XLite\Model\Repo\ConfigAbstract implements \XLite\Base\IDecorator
{
    /**
     * @param $queryBuilder
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function postprocessSearchRestAPIQueryBuilder($queryBuilder)
    {
        $queryBuilder = $this->postprocessSearchResultQueryBuilder($queryBuilder);

        if ($this->isSelfConfigForbidden()) {
            $queryBuilder->andWhere('c.category <> :restapi');
            $queryBuilder->setParameter('restapi', 'XC\\RESTAPI');
        }

        return $queryBuilder;
    }

    /**
     * Define query for findAllForREST() method
     *
     * @param integer $start  Start data frame position
     * @param integer $length Frame length
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineFindAllForRESTQuery($start, $length)
    {
        $queryBuilder = parent::defineFindAllForRESTQuery($start, $length);

        if ($this->isSelfConfigForbidden()) {
            $queryBuilder->andWhere('c.category <> :restapi');
            $queryBuilder->setParameter('restapi', 'XC\\RESTAPI');
        }

        return $queryBuilder;
    }

    /**
     * Find one entity for REST API
     *
     * @param mixed $id Entity ID
     *
     * @return \XLite\Model\AEntity
     */
    public function findOneForREST($id)
    {
        $result = $this->find($id);

        if ($this->isSelfConfigForbidden()
            && $result->getCategory() === 'XC\\RESTAPI'
        ) {
            $result = null;
        }

        return $result;
    }

    /**
     * @return bool
     */
    protected function isSelfConfigForbidden()
    {
        $authService = new \XLite\Module\XC\RESTAPI\Core\Auth\Keys(
            \XLite\Core\Config::getInstance()->XC->RESTAPI->key,
            \XLite\Core\Config::getInstance()->XC->RESTAPI->key_read
        );

        return !$authService->allowWrite(\XLite\Core\Request::getInstance()->_key);
    }
}
