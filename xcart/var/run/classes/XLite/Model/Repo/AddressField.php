<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Repo;

/**
 * The "address field" model repository
 *
 * @Api\Operation\Create(modelClass="XLite\Model\AddressField", summary="Add new address field")
 * @Api\Operation\Read(modelClass="XLite\Model\AddressField", summary="Retrieve address field by id")
 * @Api\Operation\ReadAll(modelClass="XLite\Model\AddressField", summary="Retrieve address fields by conditions")
 * @Api\Operation\Update(modelClass="XLite\Model\AddressField", summary="Update address field by id")
 * @Api\Operation\Delete(modelClass="XLite\Model\AddressField", summary="Delete address field by id")
 */
class AddressField extends \XLite\Model\Repo\Base\I18n
{
    /**
     * Allowable search params
     */
    const CND_ENABLED  = 'enabled';
    const CND_REQUIRED = 'required';
    const CND_WITHOUT_CSTATE = 'withoutCState';

    /**
     * Default 'order by' field name
     *
     * @var string
     */
    protected $defaultOrderBy = 'position';

    /**
     * Get all enabled address fields
     *
     * @return \Doctrine\ORM\PersistentCollection|integer
     */
    public function findAllEnabled()
    {
        return $this->defineFindAllQuery(true)->getResult();
    }

    /**
     * Return address field service name value
     *
     * @param \XLite\Model\AddressField $field
     *
     * @return string
     */
    public function getServiceName(\XLite\Model\AddressField $field)
    {
        return $field->getServiceName();
    }

    /**
     * Get billing address-specified required fields
     *
     * @return array
     */
    public function getBillingRequiredFields()
    {
        return $this->findRequiredFields();
    }

    /**
     * Get shipping address-specified required fields
     *
     * @return array
     */
    public function getShippingRequiredFields()
    {
        return $this->findRequiredFields();
    }

    /**
     * Get all enabled and required address fields
     *
     * @return array
     */
    public function findRequiredFields()
    {
        return array_map(
            array($this, 'getServiceName'),
            $this->defineFindAllQuery(true, true)->getResult()
        );
    }

    /**
     * Get all enabled and required address fields
     *
     * @return array
     */
    public function findEnabledFields()
    {
        return array_map(
            array($this, 'getServiceName'),
            $this->defineFindAllQuery(true)->getResult()
        );
    }

    /**
     * Find one by record
     *
     * @param array                $data   Record
     * @param \XLite\Model\AEntity $parent Parent model OPTIONAL
     *
     * @return \XLite\Model\AEntity
     */
    public function findOneByRecord(array $data, \XLite\Model\AEntity $parent = null)
    {
        if (isset($data['serviceName'])) {
            $result = $this->findOneByServiceName($data['serviceName']);

        } else {
            $result = parent::findOneByRecord($data, $parent);
        }

        return $result;
    }

    /**
     * Defined query builder
     *
     * @param boolean $enabled Enabled status
     * @param boolean $required Required status
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineFindAllQuery($enabled = null, $required = null)
    {
        $qb = $this->createQueryBuilder('a');

        if (isset($enabled)) {
            $this->prepareCndEnabled($qb, $enabled, false);
        }

        if (isset($required)) {
            $this->prepareCndRequired($qb, $required, false);
        }

        $qb->addOrderBy('a.position', 'ASC');
        $qb->addOrderBy('a.id', 'ASC');

        return $qb;
    }

    /**
     * Prepare query builder for enabled status search
     * @Api\Condition(description="Filters address fields by enabled\disabled flag", type="boolean")
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param boolean                    $value
     * @param boolean                    $countOnly
     *
     * @return void
     */
    protected function prepareCndEnabled(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        $queryBuilder
            ->andWhere($this->getMainAlias($queryBuilder) . '.enabled = :enabled_value')
            ->setParameter('enabled_value', $value);
    }

    /**
     * Prepare query builder for required status search
     * @Api\Condition(description="Filters address fields by required/not required flag", type="boolean")
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param boolean                    $value
     * @param boolean                    $countOnly
     *
     * @return void
     */
    protected function prepareCndRequired(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        $queryBuilder
            ->andWhere($this->getMainAlias($queryBuilder) . '.required = :required_value')
            ->setParameter('required_value', $value);
    }

    /**
     * Prepare query builder for required status search
     * @Api\Condition(description="Removes custom_state field from selection", type="boolean")
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param boolean                    $value
     * @param boolean                    $countOnly
     *
     * @return void
     */
    protected function prepareCndWithoutCState(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        if ($value) {
            $queryBuilder
                ->andWhere($this->getMainAlias($queryBuilder) . '.serviceName != :cstate')
                ->setParameter('cstate', 'custom_state');
        }
    }
}
