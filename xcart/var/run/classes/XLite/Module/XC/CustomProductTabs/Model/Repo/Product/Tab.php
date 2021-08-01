<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CustomProductTabs\Model\Repo\Product;

/**
 * @Api\Operation\Create(modelClass="XLite\Module\XC\CustomProductTabs\Model\Product\Tab", summary="Add product tab")
 * @Api\Operation\Read(modelClass="XLite\Module\XC\CustomProductTabs\Model\Product\Tab", summary="Retrieve product tab by id")
 * @Api\Operation\ReadAll(modelClass="XLite\Module\XC\CustomProductTabs\Model\Product\Tab", summary="Retrieve product tabs by conditions")
 * @Api\Operation\Update(modelClass="XLite\Module\XC\CustomProductTabs\Model\Product\Tab", summary="Update product tab by id")
 * @Api\Operation\Delete(modelClass="XLite\Module\XC\CustomProductTabs\Model\Product\Tab", summary="Delete product tab by id")
 *
 * @SWG\Tag(
 *   name="XC\CustomProductTabs\Product\Tab",
 *   x={"display-name": "Product\Tab", "group": "XC\CustomProductTabs"},
 *   description="This repo stores product-specific tabs.",
 * )
 */
class Tab extends \XLite\Model\Repo\Base\I18n
{
    /**
     * Allowable search params
     */
    const SEARCH_PRODUCT = 'product';
    const P_POSITION     = 'position';

    // {{{ Search

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param mixed                      $value        Condition data
     *
     * @return void
     */
    protected function prepareCndProduct(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if ($value && is_object($value)) {
            $queryBuilder->andWhere('t.product = :product')
                ->setParameter('product', $value)
                ->orderBy('t.position');
        }
    }

    /**
     * @param \XLite\Module\XC\CustomProductTabs\Model\Product\Tab $tab
     *
     * @return string
     */
    public function generateTabLink(\XLite\Module\XC\CustomProductTabs\Model\Product\Tab $tab)
    {
        $result = $link = preg_replace(
            '/[^a-z0-9-_]/i',
            '',
            str_replace(
                ' ',
                '_',
                \XLite\Core\Converter::convertToTranslit($tab->getName())
            )
        );

        $i = 1;
        while (!$this->checkLinkUniqueness($result, $tab)) {
            $result = $link . '_' . $i;
            $i++;
        }

        return $result;
    }

    /**
     * @param string                                               $link
     * @param \XLite\Module\XC\CustomProductTabs\Model\Product\Tab $tab
     *
     * @return bool
     */
    public function checkLinkUniqueness($link, \XLite\Module\XC\CustomProductTabs\Model\Product\Tab $tab)
    {
        $result = !$this->findOneBy([
                'link'    => $link,
                'product' => $tab->getProduct(),
            ]) && !\XLite\Core\Database::getRepo('XLite\Model\Product\GlobalTab')->findOneBy([
                'link' => $link,
            ]) && !\XLite\Core\Database::getRepo('XLite\Model\Product\GlobalTab')->findOneBy([
                'service_name' => $link,
            ]);

        return $result;
    }


    // }}}

    public function loadFixture(array $record, array $regular = [], array $assocs = [], \XLite\Model\AEntity $parent = null, array $parentAssoc = [])
    {
        $entity = parent::loadFixture($record, $regular, $assocs, $parent, $parentAssoc);

        if (
            !$entity->isGlobal()
            && !$entity->getLink()
        ) {

            $entity->setLink(
                \XLite\Core\Database::getRepo('\XLite\Module\XC\CustomProductTabs\Model\Product\Tab')
                    ->generateTabLink($entity)
            );
        }

        return $entity;
    }
}