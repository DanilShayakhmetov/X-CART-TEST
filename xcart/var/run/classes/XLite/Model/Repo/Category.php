<?php
namespace XLite\Model\Repo;
/**
 * Category repository class
 *
 * @Api\Operation\Create(modelClass="XLite\Model\Category", summary="Add new category")
 * @Api\Operation\Read(modelClass="XLite\Model\Category", summary="Retrieve category by id")
 * @Api\Operation\ReadAll(modelClass="XLite\Model\Category", summary="Retrieve categories by conditions")
 * @Api\Operation\Update(modelClass="XLite\Model\Category", summary="Update category by id")
 * @Api\Operation\Delete(modelClass="XLite\Model\Category", summary="Delete category by id")
 */
class Category extends \XLite\Module\CDev\GoSocial\Model\Repo\Category {}