<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductVariants\Core\ConsistencyCheck\Rules;

use XLite\Core\ConsistencyCheck\DefaultModelStringifier;
use XLite\Core\ConsistencyCheck\Inconsistency;
use XLite\Core\ConsistencyCheck\InconsistencyEntities;
use XLite\Core\ConsistencyCheck\RuleInterface;
use XLite\Model\QueryBuilder\AQueryBuilder;
use XLite\Module\XC\ProductVariants\Model\Repo;

class AttributesRule implements RuleInterface
{
    use DefaultModelStringifier;

    /**
     * @var Repo\ProductVariant
     */
    private $repo;

    public function __construct(Repo\ProductVariant $repo)
    {
        $this->repo = $repo;
    }

    /**
     * @return Inconsistency|boolean
     */
    public function execute()
    {
        $invalid = $this->getVariantsWithoutAttributes();

        if ($invalid) {
            $message = \XLite\Core\Translation::getInstance()->translate(
                'There are %model% without valid %another_model% relation',
                [
                    'model'         => 'variants (XLite\Module\XC\ProductVariants\Model\ProductVariant)',
                    'another_model' => 'attributes (XLite\Model\Attribute)',
                ]
            );
            return new InconsistencyEntities(
              Inconsistency::ERROR,
              $message,
              array_map(function($v) {
                  return $this->stringifyModel($v);
              }, $invalid)
            );
        }

        return false;
    }

    /**
     * @return array
     */
    protected function getVariantsWithoutAttributes()
    {
        /** @var AQueryBuilder $qb */
        $qb = $this->repo->createPureQueryBuilder('v');

        $qb->linkLeft('v.attributeValueC', 'valueC');
        $qb->linkLeft('v.attributeValueS', 'valueS');

        $qb->linkLeft('valueC.attribute', 'CAttribute');
        $qb->linkLeft('valueS.attribute', 'SAttribute');

        $qb->linkLeft('CAttribute.variantsProducts', 'CVariantsProducts');
        $qb->linkLeft('SAttribute.variantsProducts', 'SVariantsProducts');

        $productEqualsCnd = $qb->expr()->andX();
        $productEqualsCnd->add('v.product NOT MEMBER OF CAttribute.variantsProducts');
        $productEqualsCnd->add('v.product NOT MEMBER OF SAttribute.variantsProducts');

        $qb->andWhere($productEqualsCnd);

        return $qb->getResult();
    }
}
