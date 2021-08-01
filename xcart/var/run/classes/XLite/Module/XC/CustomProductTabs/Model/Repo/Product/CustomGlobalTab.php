<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CustomProductTabs\Model\Repo\Product;

/**
 * @Api\Operation\Create(modelClass="XLite\Module\XC\CustomProductTabs\Model\Product\CustomGlobalTab", summary="Add custom global tab")
 * @Api\Operation\Read(modelClass="XLite\Module\XC\CustomProductTabs\Model\Product\CustomGlobalTab", summary="Retrieve custom global tab by id")
 * @Api\Operation\ReadAll(modelClass="XLite\Module\XC\CustomProductTabs\Model\Product\CustomGlobalTab", summary="Retrieve custom global tabs by conditions")
 * @Api\Operation\Update(modelClass="XLite\Module\XC\CustomProductTabs\Model\Product\CustomGlobalTab", summary="Update custom global tab by id")
 * @Api\Operation\Delete(modelClass="XLite\Module\XC\CustomProductTabs\Model\Product\CustomGlobalTab", summary="Delete custom global tab by id")
 *
 * @SWG\Tag(
 *   name="XC\CustomProductTabs\Product\CustomGlobalTab",
 *   x={"display-name": "Product\CustomGlobalTab", "group": "XC\CustomProductTabs"},
 *   description="This repo stores user-created global product tabs.",
 * )
 */
class CustomGlobalTab extends \XLite\Model\Repo\Base\I18n
{
    public function loadFixture(array $record, array $regular = [], array $assocs = [], \XLite\Model\AEntity $parent = null, array $parentAssoc = [])
    {
        $entity = parent::loadFixture($record, $regular, $assocs, $parent, $parentAssoc);

        if (
            $entity->getGlobalTab()
            && !$entity->getGlobalTab()->getLink()
        ) {
            $entity->getGlobalTab()->setLink(
                \XLite\Core\Database::getRepo('\XLite\Model\Product\GlobalTab')->generateTabLink($entity)
            );
        }

        return $entity;
    }
}