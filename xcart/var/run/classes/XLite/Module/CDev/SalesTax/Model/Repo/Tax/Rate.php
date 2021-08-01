<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\SalesTax\Model\Repo\Tax;

/**
 * @Api\Operation\Create(modelClass="XLite\Module\CDev\SalesTax\Model\Tax\Rate", summary="Add tax rate")
 * @Api\Operation\Read(modelClass="XLite\Module\CDev\SalesTax\Model\Tax\Rate", summary="Retrieve tax rate by id")
 * @Api\Operation\ReadAll(modelClass="XLite\Module\CDev\SalesTax\Model\Tax\Rate", summary="Retrieve tax rates by conditions")
 * @Api\Operation\Update(modelClass="XLite\Module\CDev\SalesTax\Model\Tax\Rate", summary="Update tax rate by id")
 * @Api\Operation\Delete(modelClass="XLite\Module\CDev\SalesTax\Model\Tax\Rate", summary="Delete tax rate by id")
 *
 * @SWG\Tag(
 *   name="CDev\SalesTax\Tax\Rate",
 *   x={"display-name": "Tax\Rate", "group": "CDev\SalesTax"},
 *   description="Tax\Rate represents single tax rate record associated with the Sales tax type.",
 *   @SWG\ExternalDocumentation(
 *     description="Find out more about setting up taxes",
 *     url="https://kb.x-cart.com/en/taxes/"
 *   )
 * )
 */
class Rate extends \XLite\Model\Repo\ARepo
{
    /**
     * Search params
     */
    const PARAM_TAXABLE_BASE      = 'taxableBase';
    const PARAM_EXCL_TAXABLE_BASE = 'excludeTaxableBase';

    /**
     * Prepare certain search condition
     * @Api\Condition(description="Filters rates by taxable base", type="string", enum={"ST","DST","ST+SH","DST+SH","SH","P"})
     *
     * @param \Doctrine\ORM\QueryBuilder $qb    Query builder to prepare
     * @param mixed                      $value Condition data
     *
     * @return void
     */
    protected function prepareCndTaxableBase(\Doctrine\ORM\QueryBuilder $qb, $value)
    {
        $qb->andWhere('r.taxableBase = :taxableBase')
            ->setParameter('taxableBase', $value);
    }

    /**
     * Prepare certain search condition
     * @Api\Condition(description="Filters rates without certain taxable base", type="string", enum={"ST","DST","ST+SH","DST+SH","SH","P"})
     *
     * @param \Doctrine\ORM\QueryBuilder $qb    Query builder to prepare
     * @param mixed                      $value Condition data
     *
     * @return void
     */
    protected function prepareCndExcludeTaxableBase(\Doctrine\ORM\QueryBuilder $qb, $value)
    {
        $list = (!is_array($value) ? array($value) : $value);

        foreach ($list as $k => $val) {
            if (empty($val)) {
                unset($list[$k]);
            }
        }

        if (!empty($list)) {
            if (1 == count($list)) {
                $qb->andWhere('r.taxableBase != :taxableBase')
                    ->setParameter('taxableBase', $list[0]);

            } else {
                $qb->andWhere('r.taxableBase NOT IN (:taxableBase)')
                    ->setParameter('taxableBase', $list);
            }
        }
    }
}
