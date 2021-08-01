<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\ConsistencyCheck\Rules\AttributeValue\AttributeValueSelect;

use XLite\Core\ConsistencyCheck\Inconsistency;
use XLite\Core\ConsistencyCheck\InconsistencyEntities;
use XLite\Core\ConsistencyCheck\RuleInterface;
use XLite\Model\QueryBuilder\AQueryBuilder;
use XLite\Model\Repo;

class AttributeOptionExistsRule implements RuleInterface
{
    use AttributeValueSelectStringifier;

    /**
     * @var Repo\Order
     */
    private $repo;

    /**
     * SurchargesRule constructor.
     *
     * @param Repo\AttributeValue\AttributeValueSelect $repo
     */
    public function __construct(Repo\AttributeValue\AttributeValueSelect $repo)
    {
        $this->repo = $repo;
    }

    /**
     * @return Inconsistency|bool
     */
    public function execute()
    {
        $invalid = $this->getAttributeValuesWithoutExistingAttributeOptions();

        if ($invalid) {
            $message = \XLite\Core\Translation::getInstance()->translate(
                'There are %model% with missing %another_model%',
                [
                    'model'         => 'Attribute values (select) (XLite\Model\AttributeValue\AttributeValueSelect)',
                    'another_model' => 'Attribute option (XLite\Model\AttributeOption)',
                ]
            );
            return new InconsistencyEntities(
                Inconsistency::ERROR,
                $message,
                array_map(function($v){
                    return $this->stringifyModel($v);
                }, $invalid)
            );
        }

        return false;
    }

    /**
     * @return array
     */
    protected function getAttributeValuesWithoutExistingAttributeOptions()
    {
        /** @var AQueryBuilder $qb */
        $qb = $this->repo->createPureQueryBuilder('a');
        $qb->linkLeft('a.attribute_option', 'ao');
        $qb->andWhere('a.attribute_option IS NOT NULL');
        $qb->andWhere('ao.id IS NULL');

        return $qb->getResult();
    }
}