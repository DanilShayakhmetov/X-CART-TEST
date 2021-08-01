<?php
namespace XLite\Model\Repo\AttributeValue;
/**
 * Attribute values repository
 *
 * @Api\Operation\Create(modelClass="XLite\Model\AttributeValue\AttributeValueText", summary="Add new text attribute value")
 * @Api\Operation\Read(modelClass="XLite\Model\AttributeValue\AttributeValueText", summary="Retrieve text attribute value by id")
 * @Api\Operation\ReadAll(modelClass="XLite\Model\AttributeValue\AttributeValueText", summary="Retrieve text attribute values by conditions")
 * @Api\Operation\Update(modelClass="XLite\Model\AttributeValue\AttributeValueText", summary="Update text attribute value by id")
 * @Api\Operation\Delete(modelClass="XLite\Model\AttributeValue\AttributeValueText", summary="Delete text attribute value by id")
 */
class AttributeValueText extends \XLite\Module\XC\GoogleFeed\Model\Repo\AttributeValue\AttributeValueText {}