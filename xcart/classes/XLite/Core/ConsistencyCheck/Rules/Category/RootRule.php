<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\ConsistencyCheck\Rules\Category;

use Doctrine\ORM\ORMException;
use XLite\Core\ConsistencyCheck\DefaultModelStringifier;
use XLite\Core\ConsistencyCheck\Inconsistency;
use XLite\Core\ConsistencyCheck\InconsistencyEntities;
use XLite\Core\ConsistencyCheck\RuleInterface;
use XLite\Model\Repo\ARepo;

class RootRule implements RuleInterface
{
    use DefaultModelStringifier;

    /**
     * @var ARepo
     */
    private $repo;

    public function __construct(ARepo $repo)
    {
        $this->repo = $repo;
    }

    /**
     * @return Inconsistency|boolean
     */
    public function execute()
    {
        try {
            $valid = $this->getValidRootCategory();

        } catch (ORMException $e) {
            $valid = false;
        }

        $possible = $this->getPossibleRootCategories();

        $possibleCategories = [];

        if (is_array($possible) && (count($possible) >1 || !$valid)) {
            $possibleCategories = array_map(function($c) {
                return $this->stringifyModel($c);
            }, $possible);
        }

        $result = false;

        if ($possibleCategories) {
            if ($valid) {
                $message = \XLite\Core\Translation::getInstance()->translate(
                    'There is one valid root category, however there are multiple root category candidates'
                );
                $possibleCategories = array_filter($possibleCategories, function($c) use ($valid) {
                   return $c !== $this->stringifyModel($valid);
                });
                $result = new InconsistencyEntities(
                    Inconsistency::WARNING,
                    $message,
                    $possibleCategories
                );

            } else {
                $message = \XLite\Core\Translation::getInstance()->translate(
                    'We couldn\'t find one correct root category, however there are possible candidates'
                );
                $result = new InconsistencyEntities(
                    Inconsistency::WARNING,
                    $message,
                    $possibleCategories
                );
            }

        } elseif (!$valid) {
            $message = \XLite\Core\Translation::getInstance()->translate(
                'We couldn\'t find one correct root category'
            );
            $result = new Inconsistency(
                Inconsistency::ERROR,
                $message
            );
        }

        return $result;
    }

    /**
     * @return \XLite\Model\Category
     */
    protected function getValidRootCategory()
    {
        $qb = $this->repo->createPureQueryBuilder('c');

        $cnd = $qb->expr()->andX();
        $cnd->add('c.parent IS NULL');
        $cnd->add('c.depth = :depth');
        $cnd->add('c.lpos = :lpos');

        $qb->where($cnd);

        $qb->setParameter('depth', -1);
        $qb->setParameter('lpos', 1);

        return $qb->getQuery()->getSingleResult();
    }

    /**
     * @return array
     */
    protected function getPossibleRootCategories()
    {
        $qb = $this->repo->createPureQueryBuilder('c');

        $cnd = $qb->expr()->orX();
        $cnd->add('c.parent IS NULL');
        $cnd->add('c.depth = :depth');
        $cnd->add('c.lpos = :lpos');
        $cnd->add('c.parent = c.category_id');

        $qb->where($cnd);

        $qb->setParameter('depth', -1);
        $qb->setParameter('lpos', 1);

        return $qb->getQuery()->getResult();
    }
}
