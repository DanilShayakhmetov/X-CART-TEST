<?php
namespace XLite\Model\Repo;
/**
 * Country repository
 *
 * @Api\Operation\Create(modelClass="XLite\Model\Country", summary="Add new country")
 * @Api\Operation\Read(modelClass="XLite\Model\Country", summary="Retrieve country by id")
 * @Api\Operation\ReadAll(modelClass="XLite\Model\Country", summary="Retrieve countries by conditions")
 * @Api\Operation\Update(modelClass="XLite\Model\Country", summary="Update country by id")
 * @Api\Operation\Delete(modelClass="XLite\Model\Country", summary="Delete country by id")
 */
class Country extends \XLite\Module\XC\FastLaneCheckout\Model\Repo\Country {}