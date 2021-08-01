<?php
namespace XLite\Model\Repo;
/**
 * Attributes repository
 *
 * @Api\Operation\Create(modelClass="XLite\Model\Attribute", summary="Add new product attribute")
 * @Api\Operation\Read(modelClass="XLite\Model\Attribute", summary="Retrieve product attribute by id")
 * @Api\Operation\ReadAll(modelClass="XLite\Model\Attribute", summary="Retrieve product attributes by conditions")
 * @Api\Operation\Update(modelClass="XLite\Model\Attribute", summary="Update product attribute by id")
 * @Api\Operation\Delete(modelClass="XLite\Model\Attribute", summary="Delete product attribute by id")
 */
class Attribute extends \XLite\Module\XC\GoogleFeed\Model\Repo\Attribute {}