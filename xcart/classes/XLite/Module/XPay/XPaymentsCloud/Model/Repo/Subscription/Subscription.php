<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
namespace XLite\Module\XPay\XPaymentsCloud\Model\Repo\Subscription;

use XLite\Module\XPay\XPaymentsCloud\Core\Converter as Converter;
use XLite\Module\XPay\XPaymentsCloud\Model\Subscription\Subscription as SubscriptionModel;

/**
 * Subscriptions repository
 */
class Subscription extends \XLite\Model\Repo\ARepo
{
    // {{{ Search

    const SEARCH_LIMIT           = 'limit';
    const SEARCH_ORDER           = 'order';
    const SEARCH_PROFILE         = 'profile';
    const SEARCH_ID              = 'id';
    const SEARCH_CARD_ID         = 'cardId';
    const SEARCH_PRODUCT_NAME    = 'productName';
    const SEARCH_STATUS          = 'status';
    const SEARCH_DATE_RANGE      = 'dateRange';
    const SEARCH_NEXT_DATE_RANGE = 'nextDateRange';
    const SEARCH_ACTUAL_DATE       = 'actualDate';

    const SEARCH_PAY_TODAY       = 'payToday';

    const SEARCH_ORDER_BY        = 'orderBy';

    const STATUS_ANY           = '';
    const STATUS_EXPIRED       = 'E';
    const STATUS_ACTIVE_FAILED = 'AF';
    const STATUS_ACTIVE        = 'A';
    const STATUS_ACTIVE_OR_PENDING = 'AP';
    const STATUS_ACTIVE_OR_RESTARTED = 'AR';

    /**
     * Returns first active subscription by card
     *
     * @return \XLite\Module\XPay\Backup\Model\BackupFile
     */
    public function findOneActiveByCardId($cardId, $includePending = false)
    {
        $cnd = new \XLite\Core\CommonCell;
        $cnd->{static::SEARCH_CARD_ID} = $cardId;
        $cnd->{static::SEARCH_STATUS} = ($includePending)
            ? static::STATUS_ACTIVE_OR_PENDING
            : SubscriptionModel::STATUS_ACTIVE;
        $cnd->{static::P_LIMIT} = [0, 1];

        $result = $this->search($cnd);

        return current($result);
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array                      $value        Condition data
     *
     * @return void
     */
    protected function prepareCndCardId(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if ($value) {
            $queryBuilder->andWhere('s.card = :cardId')
                ->setParameter('cardId', $value);
        }
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array                      $value        Condition data
     *
     * @return void
     */
    protected function prepareCndLimit(\Doctrine\ORM\QueryBuilder $queryBuilder, array $value)
    {
        $queryBuilder->setFrameResults($value);
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param \XLite\Model\Order         $value        Condition data
     *
     * @return void
     */
    protected function prepareCndOrder(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        $queryBuilder->linkInner('XLite\Model\OrderItem', 'oi', 'WITH', 'oi.xpaymentsSubscription = s.id')
            ->andWhere('oi.order = :order')
            ->setParameter('order', $value);
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param \XLite\Model\Profile       $value        Condition data
     *
     * @return void
     */
    protected function prepareCndProfile(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        $queryBuilder->linkInner('s.initialOrderItem')
            ->linkInner('initialOrderItem.order', 'o')
            ->andWhere('o.orig_profile = :profile')
            ->setParameter('profile', $value);
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param string                     $value        Condition data
     *
     * @return void
     */
    protected function prepareCndId(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if ($value) {
            $queryBuilder->linkInner('s.initialOrderItem')
                ->linkInner('initialOrderItem.order', 'initialOrder')
                ->andWhere(
                    $queryBuilder->expr()->orX(
                        's.id = :id',
                        'initialOrder.orderNumber = :id'
                    )
                )
                ->setParameter('id', $value);
        }
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param string                     $value        Condition data
     *
     * @return void
     */
    protected function prepareCndProductName(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if ($value) {
            $queryBuilder->linkInner('XLite\Model\OrderItem', 'oi')
                ->andWhere('oi.name LIKE :name')
                ->setParameter('name', '%' . $value . '%');
        }
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param string                     $value        Condition data
     *
     * @return void
     */
    protected function prepareCndStatus(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if ($value) {
            switch ($value) {
                case static::STATUS_ACTIVE_FAILED:
                    $value = SubscriptionModel::STATUS_ACTIVE;
                    $queryBuilder->andWhere('s.failedAttempts != 0');
                    break;

                case static::STATUS_EXPIRED:
                    $value = SubscriptionModel::STATUS_ACTIVE;
                    $queryBuilder->andWhere('s.actualDate < :currentDate')
                        ->setParameter('currentDate', Converter::now());
                    break;

                default:
            }
            if (
                static::STATUS_ACTIVE_OR_PENDING == $value
                || static::STATUS_ACTIVE_OR_RESTARTED == $value
            ) {
                $cnd = new \Doctrine\ORM\Query\Expr\Orx();
                $cnd->add('s.status = :statusActive');
                $cnd->add('s.status = :statusRestarted');
                if (static::STATUS_ACTIVE_OR_PENDING == $value) {
                    $cnd->add('s.status = :statusNotStarted');
                }

                $queryBuilder->andWhere($cnd)
                    ->setParameter('statusActive', SubscriptionModel::STATUS_ACTIVE)
                    ->setParameter('statusRestarted', SubscriptionModel::STATUS_RESTARTED);
                if (static::STATUS_ACTIVE_OR_PENDING == $value) {
                    $queryBuilder
                        ->setParameter('statusNotStarted', SubscriptionModel::STATUS_NOT_STARTED);
                }

            } else {
                $queryBuilder->andWhere('s.status = :status')
                    ->setParameter('status', $value);

            }
        }
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param string                     $value        Condition data
     *
     * @return void
     */
    protected function prepareCndDateRange(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if (!empty($value)) {
            list($start, $end) = \XLite\View\FormField\Input\Text\DateRange::convertToArray($value);

            if ($start) {
                $queryBuilder->andWhere('s.startDate >= :start')
                    ->setParameter('start', $start);
            }

            if ($end) {
                $queryBuilder->andWhere('s.startDate <= :end')
                    ->setParameter('end', $end);
            }
        }
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param string                     $value        Condition data
     *
     * @return void
     */
    protected function prepareCndNextDateRange(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if (!empty($value)) {
            list($start, $end) = \XLite\View\FormField\Input\Text\DateRange::convertToArray($value);

            if ($start) {
                $queryBuilder->andWhere('s.actualDate >= :start')
                    ->setParameter('start', $start);
            }

            if ($end) {
                $queryBuilder->andWhere('s.actualDate <= :end')
                    ->setParameter('end', $end);
            }
        }
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param string                     $value        Condition data
     *
     * @return void
     */
    protected function prepareCndActualDate(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        $queryBuilder->andWhere('s.actualDate = :actualDate')
            ->setParameter('actualDate', $value);
    }

    /**
     * Find subscriptions which should be payed today
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param string                     $value        Condition data
     *
     * @return void
     */
    protected function prepareCndPayToday(\Doctrine\ORM\QueryBuilder $queryBuilder, $value = null)
    {
        $today = Converter::convertTimeToDayStart($value);

        // Actual Date must be in future, so if it's in past (for more than one day)
        // it means that some subscriptions were missing (skiped)
        $queryBuilder->andWhere('s.actualDate <= :today')->setParameter('today', $today);

        $cnd = new \Doctrine\ORM\Query\Expr\Orx();
        $cnd->add('s.status = :statusActive');
        $cnd->add('s.status = :statusRestarted');
        $queryBuilder->andWhere($cnd)
            ->setParameter('statusActive', SubscriptionModel::STATUS_ACTIVE)
            ->setParameter('statusRestarted', SubscriptionModel::STATUS_RESTARTED);
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array                      $value        Condition data
     *
     * @return void
     */
    protected function prepareCndOrderBy(\Doctrine\ORM\QueryBuilder $queryBuilder, array $value)
    {
        list($sort, $order) = $this->getSortOrderValue($value);

        switch ($sort) {
            case 'profile.email':
            case 'profile.login':
                $queryBuilder->linkInner('s.initialOrderItem')
                    ->linkInner('initialOrderItem.order', 'initialOrder')
                    ->linkInner('initialOrder.profile');
                break;

            case 'initialOrderItem.name':
                $queryBuilder->linkInner('s.initialOrderItem');
                break;

            default:
                break;
        }

        $queryBuilder->addOrderBy($sort, $order);
    }

    // }}}

    /**
     * Update single entity
     *
     * @param \XLite\Model\AEntity $entity Entity to use
     * @param array                $data   Data to save OPTIONAL
     *
     * @return void
     */
    protected function performUpdate(\XLite\Model\AEntity $entity, array $data = array())
    {
        parent::performUpdate($entity, $data);

        $entity->checkStatuses();
    }

    /**
     * @return \XLite\Core\CommonCell
     */
    public function defineCndDumpSubscription()
    {
        $cnd = new \XLite\Core\CommonCell();
        $cnd->{static::SEARCH_ORDER_BY} = ['s.startDate', 'desc'];
        $cnd->{static::SEARCH_LIMIT} = [0, 1];

        return $cnd;
    }

    /**
     * @return null|SubscriptionModel
     */
    public function findDumpSubscription()
    {
        $cnd = $this->defineCndDumpSubscription();
        $result = $this->search($cnd);

        return count($result) ? $result[0] : null;
    }

}
