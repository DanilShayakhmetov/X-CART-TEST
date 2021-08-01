<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Repo\Payment;

/**
 * Transaction repository
 *
 * @Api\Operation\Create(modelClass="XLite\Model\Payment\Transaction", summary="Add new payment transaction")
 * @Api\Operation\Read(modelClass="XLite\Model\Payment\Transaction", summary="Retrieve payment transaction by id")
 * @Api\Operation\ReadAll(modelClass="XLite\Model\Payment\Transaction", summary="Retrieve payment transactions by conditions")
 * @Api\Operation\Update(modelClass="XLite\Model\Payment\Transaction", summary="Update payment transaction by id")
 * @Api\Operation\Delete(modelClass="XLite\Model\Payment\Transaction", summary="Delete payment transaction by id")
 */
class Transaction extends \XLite\Model\Repo\ARepo
{

    const SEARCH_ORDER     = 'order';
    const SEARCH_SUBSTRING = 'substring';
    const SEARCH_PUBLIC_ID = 'public_id';
    const SEARCH_DATE      = 'date';
    const SEARCH_STATUS    = 'status';
    const SEARCH_VALUE     = 'value';
    
    const SEARCH_ZIPCODE   = 'zipcode';
    const SEARCH_CUSTOMER_NAME = 'customerName';

    const SEARCH_EXCLUDE_INITIALIZED  = 'exclude_initialized';

    /**
     * Find transaction by data cell 
     * 
     * @param string $name  Name
     * @param string $value Value
     *  
     * @return \XLite\Model\Payment\Transaction
     */
    public function findOneByCell($name, $value)
    {
        return $this->defineFindOneByCellQuey($name, $value)->getSingleResult();
    }

    /**
     * Define query for findOneByCell() method
     * 
     * @param string $name  Name
     * @param string $value Value
     *  
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineFindOneByCellQuey($name, $value)
    {
        return $this->createQueryBuilder('p')
            ->linkInner('p.data')
            ->andWHere('data.name = :name AND data.value = :value')
            ->setParameter('name', $name)
            ->setParameter('value', $value)
            ->setMaxResults(1);
    }

    /**
     * Prepare certain search condition
     * @Api\Condition(description="Filter transactions by order id", type="integer")
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param mixed                      $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag. Do not need to add "order by" clauses if only count is needed.
     *
     * @return void
     */
    protected function prepareCndOrder(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        if ($value) {
            if (is_object($value)) {
                $queryBuilder->andWhere('t.order = :order')
                    ->setParameter('order', $value);

            } else {
                $queryBuilder->linkInner('t.order', 'ordr');
                $queryBuilder->andWhere('ordr.orderNumber = :orderNumber')
                    ->setParameter('orderNumber', $value);
            }
        }
    }

    /**
     * Prepare certain search condition
     * @Api\Condition(description="Filter transactions by user login or order number", type="string")
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param mixed                      $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag. Do not need to add "order by" clauses if only count is needed.
     *
     * @return void
     */
    protected function prepareCndSubstring(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        if (!empty($value)) {
            $number = $value;
            if (preg_match('/^\d+$/S', $number)) {
                $number = (int)$number ;
            }

            $queryBuilder->linkInner('t.order', 'ordr');
            $queryBuilder->linkLeft('ordr.profile', 'p');

            $queryBuilder->andWhere('ordr.orderNumber = :substring OR p.login LIKE :substringLike')
                ->setParameter('substring', $number)
                ->setParameter('substringLike', '%' . $value . '%');
        }
    }

    /**
     * Prepare certain search condition
     * @Api\Condition(description="Filter transactions by public id", type="string")
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array|string               $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag. Do not need to add "order by" clauses if only count is needed.
     *
     * @return void
     */
    protected function prepareCndPublicId(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        if ($value) {
            $queryBuilder->andWhere('t.public_id LIKE :public_id')
                ->setParameter('public_id', '%' . $value . '%');
        }
    }

    /**
     * Prepare certain search condition
     * @Api\Condition(description="Filters transactions by date in format of d-M-Y ~ d-M-Y", type="string")
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array                      $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag. Do not need to add "order by" clauses if only count is needed.
     *
     * @return void
     */
    protected function prepareCndDate(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        if ($value) {
            $value = \XLite\View\FormField\Input\Text\DateRange::convertToArray($value);

            if (!empty($value[0])) {
                $queryBuilder->andWhere('t.date > :date0')
                    ->setParameter('date0', $value[0]);
            }

            if (!empty($value[1])) {
                $queryBuilder->andWhere('t.date < :date1')
                    ->setParameter('date1', $value[1]);
            }
        }
    }

    /**
     * Prepare certain search condition
     * @Api\Condition(description="Exclude initialized transactions", type="boolean")
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array|string               $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag. Do not need to add "order by" clauses if only count is needed.
     *
     * @return void
     */
    protected function prepareCndExcludeInitialized(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        if ($value) {
            $queryBuilder->andWhere('t.status <> :excStatus')
                ->setParameter('excStatus', \XLite\Model\Payment\Transaction::STATUS_INITIALIZED);
        }
    }

    /**
     * Prepare certain search condition
     * @Api\Condition(description="Filters transactions by status", type="string", enum={"I","P","W","S","F","C","V"})
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array|string               $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag. Do not need to add "order by" clauses if only count is needed.
     *
     * @return void
     */
    protected function prepareCndStatus(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        if ($value) {
            if (is_array($value)) {
                $queryBuilder->andWhere($queryBuilder->expr()->in('t.status', $value));

            } else {
                $queryBuilder->andWhere('t.status = :status')
                    ->setParameter('status', $value);
            }
        }
    }

    /**
     * @Api\Condition(description="Filter transactions by amount", type="array", collectionFormat="multi", items=@Swg\Items(type="number"))
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array                      $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag. Do not need to add "order by" clauses if only count is needed.
     *
     * @return void
     */
    protected function prepareCndValue(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        if ($value && is_array($value)) {
            if (!empty($value[0])) {
                $queryBuilder->andWhere('t.value > :value0')
                    ->setParameter('value0', $value[0]);
            }

            if (!empty($value[1])) {
                $queryBuilder->andWhere('t.value < :value1')
                    ->setParameter('value1', $value[1]);
            }
        }
    }

    /**
     * @Api\Condition(description="Filter transactions by customer zipcode", type="string")
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param integer                    $value        Condition data
     *
     * @return void
     */
    protected function prepareCndZipcode(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if (!empty($value)) {
            $queryBuilder->linkInner('t.order', 'ordr');
            $queryBuilder->linkLeft('ordr.profile', 'p');
            $queryBuilder->linkLeft('p.addresses', 'addresses');

            $this->prepareAddressField($queryBuilder, 'zipcode');

            $queryBuilder->andWhere('address_field_value_zipcode.value LIKE :zipcodeValue')
                ->setParameter('zipcodeValue', '%' . $value . '%');
        }
    }

    /**
     * @Api\Condition(description="Filter transactions by address field name", type="string")
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder object
     * @param string                     $fieldName    Field name
     *
     * @return void
     */
    protected function prepareAddressField(\Doctrine\ORM\QueryBuilder $queryBuilder, $fieldName)
    {
        $addressFieldName = 'address_field_value_' . $fieldName;

        $addressField = \XLite\Core\Database::getRepo('XLite\Model\AddressField')
            ->findOneBy(array('serviceName' => $fieldName));

        $queryBuilder->linkLeft(
            'addresses.addressFields',
            $addressFieldName,
            \Doctrine\ORM\Query\Expr\Join::WITH,
            $addressFieldName . '.addressField = :' . $fieldName
        )->setParameter($fieldName, $addressField);
    }

    /**
     * @Api\Condition(description="Filter transactions by customer name or email", type="string")
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param integer                    $value        Condition data
     *
     * @return void
     */
    protected function prepareCndCustomerName(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if (!empty($value)) {
            $queryBuilder->linkInner('t.order', 'ordr');
            $queryBuilder->linkLeft('ordr.profile', 'p');

            $queryBuilder->andWhere('p.searchFakeField LIKE :customerName')
                ->setParameter('customerName', '%' . $value . '%');
        }
    }

    // }}}

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
        $queryBuilder->linkInner('t.order', 'ordr');

        parent::prepareCndOrderBy($queryBuilder, $value);
    }

}
