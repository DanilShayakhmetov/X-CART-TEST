<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\VendorMessages\Model\Repo;

use Doctrine\ORM\Query\Parameter;
use XLite\Model\AEntity;
use XLite\Model\QueryBuilder\AQueryBuilder;

/**
 * Conversation Repository
 */
class Conversation extends \XLite\Model\Repo\ARepo
{
    const SEARCH_MESSAGES          = 'messages';
    const SEARCH_MESSAGE_SUBSTRING = 'messageSubstring';

    const P_MEMBER            = 'member';
    const P_ORDERS_ONLY       = 'ordersOnly';
    const P_ORDER_BY          = 'orderBy';
    const P_ORDERS_CONDITIONS = 'ordersConditions';

    /**
     * Find users dialogue
     *
     * @param \XLite\Model\Profile $profile1
     * @param \XLite\Model\Profile $profile2
     *
     * @return \XLite\Module\XC\VendorMessages\Model\Conversation|null
     */
    public function findDialogue($profile1, $profile2)
    {
        if ($profile1 && $profile2) {
            $qb    = $this->createQueryBuilder();
            $alias = $this->getMainAlias($qb);

            $qb->linkInner("{$alias}.members", 'memb')
                ->andWhere(":profile1 MEMBER OF {$alias}.members")
                ->andWhere(":profile2 MEMBER OF {$alias}.members")
                ->andWhere("{$alias}.order IS NULL")
                ->having("COUNT(memb) = 2")
                ->groupBy("{$alias}.id")
                ->setParameter('profile1', $profile1)
                ->setParameter('profile2', $profile2);

            return $qb->getSingleResult();
        }

        return null;
    }

    /**
     * @param array                $ids
     * @param \XLite\Model\Profile $profile
     */
    public function markRead(array $ids, $profile)
    {
        $readTable          = \XLite\Core\Database::getRepo('XLite\Module\XC\VendorMessages\Model\MessageRead')->getTableName();
        $messagesTable      = \XLite\Core\Database::getRepo('XLite\Module\XC\VendorMessages\Model\Message')->getTableName();
        $conversationsTable = $this->getTableName();

        $selectQuery = "SELECT :date, m.id, :profileId FROM {$messagesTable} m INNER JOIN {$conversationsTable} c ON c.id = m.conversation_id AND c.id IN (:identifiers)";
        $query       = "INSERT IGNORE INTO {$readTable}(date, message_id, profile_id) ($selectQuery)";

        \XLite\Core\Database::getEM()->getConnection()->executeQuery($query, [
            'profileId'   => $profile->getProfileId(),
            'identifiers' => $ids,
            'date'        => \XLite\Core\Converter::time(),
        ], [
            'profileId'   => \PDO::PARAM_INT,
            'identifiers' => \Doctrine\DBAL\Connection::PARAM_INT_ARRAY,
            'date'        => \PDO::PARAM_INT,
        ]);
    }

    /**
     * @param \XLite\Model\Profile $profile
     */
    public function markReadAll($profile)
    {
        $readTable     = \XLite\Core\Database::getRepo('XLite\Module\XC\VendorMessages\Model\MessageRead')->getTableName();
        $messagesTable = \XLite\Core\Database::getRepo('XLite\Module\XC\VendorMessages\Model\Message')->getTableName();

        $selectQuery = "SELECT :date, m.id, :profileId FROM {$messagesTable} m";

        if (!$profile->isPermissionAllowed('manage conversations')) {
            $conversationsTable = $this->getTableName();
            $tablePrefix        = \XLite::getInstance()->getOptions(['database_details', 'table_prefix']);
            $membersTable       = $tablePrefix . 'conversation_members';

            $selectQuery .= " INNER JOIN {$conversationsTable} c ON c.id = m.conversation_id LEFT JOIN {$membersTable} members ON members.conversation_id = c.id AND members.profile_id = :profileId WHERE members.profile_id IS NOT NULL";
            if ($profile->isPermissionAllowed('manage orders')) {
                $selectQuery .= ' OR c.order_id IS NOT NULL';
            }
        }

        $query = "INSERT IGNORE INTO {$readTable}(date, message_id, profile_id) ($selectQuery)";

        \XLite\Core\Database::getEM()->getConnection()->executeQuery($query, [
            'profileId' => $profile->getProfileId(),
            'date'      => \XLite\Core\Converter::time(),
        ], [
            'profileId' => \PDO::PARAM_INT,
            'date'      => \PDO::PARAM_INT,
        ]);
    }

    /**
     * @param array                $ids
     * @param \XLite\Model\Profile $profile
     */
    public function markUnread(array $ids, $profile)
    {
        $readTable          = \XLite\Core\Database::getRepo('XLite\Module\XC\VendorMessages\Model\MessageRead')->getTableName();
        $messagesTable      = \XLite\Core\Database::getRepo('XLite\Module\XC\VendorMessages\Model\Message')->getTableName();
        $conversationsTable = $this->getTableName();

        $selectQuery = "SELECT m.id FROM {$messagesTable} m INNER JOIN {$conversationsTable} c ON c.id = m.conversation_id AND c.id IN (:identifiers)";
        $query       = "DELETE r FROM {$readTable} AS r WHERE r.message_id IN ($selectQuery) AND r.profile_id = :profileId";

        \XLite\Core\Database::getEM()->getConnection()->executeQuery($query, [
            'profileId'   => $profile->getProfileId(),
            'identifiers' => $ids,
        ], [
            'profileId'   => \PDO::PARAM_INT,
            'identifiers' => \Doctrine\DBAL\Connection::PARAM_INT_ARRAY,
        ]);
    }

    /**
     * @param \XLite\Model\Profile $profile
     */
    public function markUnreadAll($profile)
    {
        $readTable = \XLite\Core\Database::getRepo('XLite\Module\XC\VendorMessages\Model\MessageRead')->getTableName();

        $query = "DELETE r FROM {$readTable} AS r WHERE r.profile_id = :profileId";

        \XLite\Core\Database::getEM()->getConnection()->executeQuery($query, [
            'profileId' => $profile->getProfileId(),
        ], [
            'profileId' => \PDO::PARAM_INT,
        ]);
    }

    protected function searchCount()
    {
        /* @var AQueryBuilder $queryBuilder */
        $queryBuilder = $this->searchState['queryBuilder'];

        if ($queryBuilder->getDQLPart('having')) {
            $sql = $queryBuilder
                ->select('1')
                ->resetDQLPart('orderBy')
                ->groupBy('c.id')
                ->getQuery()
                ->getSQL();

            $params = array_map(static function (Parameter $e) {
                if ($e->getValue() instanceof AEntity) {
                    return $e->getValue()->getUniqueIdentifier();
                }

                return $e->getValue();
            }, $queryBuilder->getParameters()->toArray());

            $stmt = \XLite\Core\Database::getEM()->getConnection()->executeQuery(
                "SELECT SUM(1) FROM ($sql) AS sq",
                $params
            );

            $list = $stmt->fetch();

            return (int) reset($list); //sorry
        }

        return parent::searchCount();
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param \XLite\Model\Profile       $value        Condition data
     *
     * @return void
     */
    protected function prepareCndMember(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if ($value) {
            $alias = $this->getMainAlias($queryBuilder);
            $queryBuilder->andWhere(":member MEMBER OF {$alias}.members")
                ->setParameter('member', $value);
        }
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param \XLite\Model\Profile       $value        Condition data
     *
     * @return void
     */
    protected function prepareCndOrdersOnly(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if ($value) {
            $alias = $this->getMainAlias($queryBuilder);
            $queryBuilder->linkInner("{$alias}.order", 'o');
        }
    }

    /**
     * Prepare certain search condition
     *
     * @param \XLite\Model\QueryBuilder\AQueryBuilder $queryBuilder Query builder to prepare
     * @param integer                                 $value        Condition data
     *
     * @return void
     */
    protected function prepareCndMessages(\XLite\Model\QueryBuilder\AQueryBuilder $queryBuilder, $value)
    {
        if (!empty($value)) {
            $alias = $this->getMainAlias($queryBuilder);
            switch ($value) {
                case 'U':
                    $queryBuilder->linkInner("{$alias}.messages")
                        ->linkLeft('messages.readers', 'r0', \Doctrine\ORM\Query\Expr\Join::WITH, 'r0.reader = :reader')
                        ->andWhere('r0.id IS NULL')
                        ->setParameter('reader', \XLite\Core\Auth::getInstance()->getProfile());
                    break;

                case 'A':
                    $queryBuilder->linkInner("{$alias}.messages");
                    break;

                case 'D':
                    $queryBuilder->linkInner("{$alias}.order", 'o');
                    if (\XLite\Module\XC\VendorMessages\Main::isAllowDisputes()) {
                        $queryBuilder->andWhere("o.is_opened_dispute = :order_dispute_state")
                            ->setParameter('order_dispute_state', true);
                    }
                    break;
            }
        }
    }

    /**
     * Prepare certain search condition
     *
     * @param \XLite\Model\QueryBuilder\AQueryBuilder $queryBuilder Query builder to prepare
     * @param string                                  $value        Condition data
     */
    protected function prepareCndMessageSubstring(\XLite\Model\QueryBuilder\AQueryBuilder $queryBuilder, $value)
    {
        if (!empty($value)) {
            $alias = $this->getMainAlias($queryBuilder);
            $queryBuilder->linkInner("{$alias}.messages")
                ->andWhere('messages.body LIKE :message_substring')
                ->setParameter('message_substring', '%' . $value . '%');
        }
    }

    /**
     * @inheritdoc
     */
    protected function prepareCndOrderBy(\Doctrine\ORM\QueryBuilder $queryBuilder, array $value)
    {
        if (is_array($value) && $value[0] == 'read_messages') {
            $alias = $this->getMainAlias($queryBuilder);
            $queryBuilder->linkInner("{$alias}.messages")
                ->linkLeft('messages.readers', 'r0', \Doctrine\ORM\Query\Expr\Join::WITH, 'r0.reader = :reader')
                ->addSelect('IF(COUNT(messages) = SUM(IF(r0.id IS NULL, 0, 1)), 1, 0) as HIDDEN read_order')
                ->addSelect('MAX(messages.date) as HIDDEN message_date_order')
                ->addOrderBy('read_order', 'asc')
                ->addOrderBy('message_date_order', 'desc')
                ->setParameter('reader', $value[2]);

        } else {
            parent::prepareCndOrderBy($queryBuilder, $value);
        }
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param \XLite\Model\Profile       $value        Condition data
     *
     * @return void
     */
    protected function prepareCndOrdersConditions(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if ($value) {
            $alias = $this->getMainAlias($queryBuilder);
            if (
                \XLite\Module\XC\VendorMessages\Main::isWarehouse()
                && \XLite\Module\XC\VendorMessages\Main::isVendorAllowedToCommunicate()
            ) {
                $queryBuilder->linkLeft("{$alias}.order", 'o')
                    ->andWhere('o.order_id IS NULL OR o.parent IS NOT NULL');
            } else {
                $queryBuilder->linkLeft("{$alias}.order", 'o')
                    ->andWhere('o.order_id IS NULL OR o.orderNumber IS NOT NULL');
            }

            if ($profile = \XLite\Core\Auth::getInstance()->getProfile()) {
                if (\XLite::isAdminZone()) {
                    $queryBuilder->andWhere('o.orig_profile != :profile');
                } else {
                    $queryBuilder->andWhere('o.orig_profile = :profile');
                }
                $queryBuilder->setParameter('profile', $profile);
            }
        }
    }
}