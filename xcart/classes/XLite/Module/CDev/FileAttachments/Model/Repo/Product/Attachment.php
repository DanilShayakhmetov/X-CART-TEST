<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\FileAttachments\Model\Repo\Product;

/**
 * @Api\Operation\Create(modelClass="XLite\Module\CDev\FileAttachments\Model\Product\Attachment", summary="Add product attachment")
 * @Api\Operation\Read(modelClass="XLite\Module\CDev\FileAttachments\Model\Product\Attachment", summary="Retrieve product attachment by id")
 * @Api\Operation\ReadAll(modelClass="XLite\Module\CDev\FileAttachments\Model\Product\Attachment", summary="Retrieve product attachments by conditions")
 * @Api\Operation\Update(modelClass="XLite\Module\CDev\FileAttachments\Model\Product\Attachment", summary="Update product attachment by id")
 * @Api\Operation\Delete(modelClass="XLite\Module\CDev\FileAttachments\Model\Product\Attachment", summary="Delete product attachment by id")
 *
 * @SWG\Tag(
 *   name="CDev\FileAttachments\Product\Attachment",
 *   x={"display-name": "Product\Attachment", "group": "CDev\FileAttachments"},
 *   description="Attachment repo holds the record on the files attached to the product",
 *   @SWG\ExternalDocumentation(
 *     description="Find out more about attachments and digital goods",
 *     url="https://kb.x-cart.com/en/products/adding_digital_goods.html"
 *   )
 * )
 */
class Attachment extends \XLite\Model\Repo\Base\I18n
{
    const P_PRODUCT = 'product';

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param mixed                      $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag. Do not need to add "order by" clauses if only count is needed.
     *
     * @return void
     */
    protected function prepareCndProduct(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        $alias = $this->getMainAlias($queryBuilder);
        if ($value instanceOf \XLite\Model\Product) {
            $queryBuilder->andWhere($alias . '.product = :product')
                ->setParameter('product', $value);

        } else {
            $queryBuilder->leftJoin($alias . '.product', 'product')
                ->andWhere('product.product_id = :productId')
                ->setParameter('productId', $value);
        }
    }

    /**
     * Returns max orderby for attachments by selected product
     *
     * @param \XLite\Model\Product $product
     *
     * @return integer
     */
    public function getMaxAttachmentOrderByForProduct(\XLite\Model\Product $product)
    {
        $qb = $this->createQueryBuilder('a');

        $qb->select('MAX(a.orderby)')
            ->andWhere('a.product = :product')
            ->setParameter('product', $product);

        return (integer)$qb->getSingleScalarResult();
    }
}
