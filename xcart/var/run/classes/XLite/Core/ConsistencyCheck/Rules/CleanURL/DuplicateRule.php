<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\ConsistencyCheck\Rules\CleanURL;

use Doctrine\ORM\ORMException;
use Doctrine\ORM\QueryBuilder;
use XLite\Core\ConsistencyCheck\DefaultModelStringifier;
use XLite\Core\ConsistencyCheck\Inconsistency;
use XLite\Core\ConsistencyCheck\InconsistencyEntities;
use XLite\Core\ConsistencyCheck\RuleInterface;
use XLite\Core\Translation;
use XLite\Model\Repo\ARepo;

class DuplicateRule implements RuleInterface
{
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
        $possible = $this->getPossibleDuplicateRecords();

        if ($possible) {
            $message = Translation::lbl(
                'There are duplicate clean URL records'
            );
            return new InconsistencyEntities(
                Inconsistency::ERROR,
                $message,
                array_map(function($v) {
                    $id = $v->getUniqueIdentifier();
                    $cleanUrl = $v->getCleanURL();
                    $link = $this->repo->buildEditURL($v->getEntity());
                    $type = $v->getEntity()->getEntityName();
                    return "<a href='{$link}' target='_blank'>{$type}</a> {$cleanUrl} (id={$id})";
                }, $possible)
            );
        }

        return false;
    }

    /**
     * @return array
     */
    protected function getPossibleDuplicateRecords()
    {
        $qb = $this->repo->createPureQueryBuilder('c');
        $qb->andWhere('c.cleanURL IN (SELECT dup.cleanURL FROM \XLite\Model\CleanURL dup GROUP BY dup.cleanURL HAVING COUNT(dup.id) > 1)');

        $result = [];
        foreach ($qb->getQuery()->getResult() as $item) {
            $url = $item->getCleanURL();
            $entity = $item->getEntity();

            if ($entity && !$this->repo->isURLUnique($url, $entity)) {
                $result[] = $item;
            }
        }

        return $result;
    }
}
