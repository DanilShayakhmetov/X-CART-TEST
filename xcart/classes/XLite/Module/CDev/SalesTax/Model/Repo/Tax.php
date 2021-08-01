<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\SalesTax\Model\Repo;

/**
 * @Api\Operation\Read(modelClass="XLite\Module\CDev\SalesTax\Model\Tax", summary="Retrieve tax type by id")
 * @Api\Operation\ReadAll(modelClass="XLite\Module\CDev\SalesTax\Model\Tax", summary="Retrieve tax types")
 * @Api\Operation\Update(modelClass="XLite\Module\CDev\SalesTax\Model\Tax", summary="Update tax type by id")
 *
 * @SWG\Tag(
 *   name="CDev\SalesTax\Tax",
 *   x={"display-name": "Tax", "group": "CDev\SalesTax"},
 *   description="Tax model keeps track of tax types, such as VAT type, Sales Tax type etc. It can have from one to many tax rate records.",
 *   @SWG\ExternalDocumentation(
 *     description="Find out more about setting up taxes",
 *     url="https://kb.x-cart.com/en/taxes/"
 *   )
 * )
 */
class Tax extends \XLite\Model\Repo\Base\I18n
{
    /**
     * Get tax
     *
     * @return \XLite\Module\CDev\SalesTax\Model\Tax
     */
    public function getTax()
    {
        $tax = $this->createQueryBuilder()
            ->setMaxResults(1)
            ->getSingleResult();

        if (!$tax) {
            $tax = $this->createTax();
        }

        return $tax;
    }

    /**
     * Find active taxes
     *
     * @return array
     */
    public function findActive()
    {
        $list = $this->defineFindActiveQuery()->getResult();
        if (0 == count($list) && 0 == count($this->findAll())) {
            $this->createTax();
            $list = $this->defineFindActiveQuery()->getResult();
        }

        return $list;
    }

    /**
     * Define query for findActive() method
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineFindActiveQuery()
    {
        return $this->createQueryBuilder()
            ->addSelect('tr')
            ->linkInner('t.rates', 'tr')
            ->andWhere('t.enabled = :true')
            ->setParameter('true', true);
    }

    /**
     * Create tax
     *
     * @return \XLite\Module\CDev\SalesTax\Model\Tax
     */
    protected function createTax()
    {
        $tax = new \XLite\Module\CDev\SalesTax\Model\Tax;
        $tax->setName('Sales tax');
        $tax->setEnabled(true);
        \XLite\Core\Database::getEM()->persist($tax);

        return $tax;
    }
}
