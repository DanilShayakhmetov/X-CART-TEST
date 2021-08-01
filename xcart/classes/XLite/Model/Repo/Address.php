<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Repo;

/**
 * The Address model repository
 *
 * @Api\Operation\Create(modelClass="XLite\Model\Address", summary="Add new address")
 * @Api\Operation\Read(modelClass="XLite\Model\Address", summary="Retrieve address by id")
 * @Api\Operation\ReadAll(modelClass="XLite\Model\Address", summary="Retrieve addresses by conditions")
 * @Api\Operation\Update(modelClass="XLite\Model\Address", summary="Update address by id")
 * @Api\Operation\Delete(modelClass="XLite\Model\Address", summary="Delete address by id")
 */
class Address extends \XLite\Model\Repo\ARepo
{
    /**
     * Repository type
     *
     * @var string
     */
    protected $type = self::TYPE_SERVICE;

    /**
     * Find the list of all cities registered in existing addresses
     *
     * @return array
     */
    public function findAllCities()
    {
        $result = $this->defineFindAllCities()->getResult();

        $cities = array();

        foreach ($result as $res) {
            $cities[] = $res->getCity();
        }

        return $cities;
    }

    /**
     * defineFindAllCities
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineFindAllCities()
    {
        return $this->createQueryBuilder()
            ->select('a.city')
            ->addGroupBy('a.city')
            ->addOrderBy('a.city');
    }

}
