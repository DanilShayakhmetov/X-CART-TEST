<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\RESTAPI\Model\Repo;

/**
 * Abstract repository
 */
abstract class ARepo extends \XLite\Model\Repo\ARepo implements \XLite\Base\IDecorator
{
    const SEARCH_MODE_RESTAPI  = 'restapi';

    /**
     * Get search modes handlers
     *
     * @return array
     */
    protected function getSearchModes()
    {
        return array_merge(
            parent::getSearchModes(),
            [
                static::SEARCH_MODE_RESTAPI     => 'searchRestApi',
            ]
        );
    }

    /**
     * Search result routine.
     *
     * @return \Doctrine\ORM\PersistentCollection
     */
    protected function searchRestApi()
    {
        $queryBuilder = $this->postprocessSearchRestAPIQueryBuilder($this->searchState['queryBuilder']);

        return $queryBuilder->getOnlyEntities();
    }

    /**
     * @param $queryBuilder
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function postprocessSearchRestAPIQueryBuilder($queryBuilder)
    {
        return $this->postprocessSearchResultQueryBuilder($queryBuilder);
    }

    /**
     * Find all entities for REST API
     * 
     * @param integer $start  Start data frame position
     * @param integer $length Frame length OPTIONAL
     *  
     * @return object
     */
    public function findAllForREST($start = 0, $length = null)
    {
        return $this->defineFindAllForRESTQuery($start, $length)->iterate();
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
        return $this->find($id);
    }

    /**
     * Process REST request 
     * 
     * @param string $method Method name
     * @param mixed  $data   Data
     *  
     * @return mixed
     */
    public function processRESTRequest($method, $data)
    {
        return $data;
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
        $qb = $this->createPureQueryBuilder();
        if (0 < $start) {
            $qb->setFirstResult($start);
        }

        if (isset($length)) {
            $qb->setMaxResults($length);
        }

        return $qb;
    }
}
