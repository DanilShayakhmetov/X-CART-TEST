<?php
namespace XLite\Model\Repo\Product;
/**
 * @Api\Operation\Create(modelClass="XLite\Model\Product\GlobalTab", summary="Add global tab")
 * @Api\Operation\Read(modelClass="XLite\Model\Product\GlobalTab", summary="Retrieve global tab by id")
 * @Api\Operation\ReadAll(modelClass="XLite\Model\Product\GlobalTab", summary="Retrieve global tabs by conditions")
 * @Api\Operation\Update(modelClass="XLite\Model\Product\GlobalTab", summary="Update global tab by id")
 * @Api\Operation\Delete(modelClass="XLite\Model\Product\GlobalTab", summary="Delete global tab by id")
 *
 * @SWG\Tag(
 *   name="Product\GlobalTab",
 *   description="This repo stores user-created global product tabs.",
 * )
 */
class GlobalTab extends \XLite\Module\XC\CustomProductTabs\Model\Repo\Product\GlobalTab {}