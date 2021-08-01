<?php
namespace XLite\Model\Repo;
/**
 * DB-based configuration registry
 *
 * @Api\Operation\Create(modelClass="XLite\Model\Config", summary="Add new config entry")
 * @Api\Operation\Read(modelClass="XLite\Model\Config", summary="Retrieve config entry by id")
 * @Api\Operation\ReadAll(modelClass="XLite\Model\Config", summary="Retrieve all config entries")
 * @Api\Operation\Update(modelClass="XLite\Model\Config", summary="Update config entry by id")
 * @Api\Operation\Delete(modelClass="XLite\Model\Config", summary="Delete config entry by id")
 */
class Config extends \XLite\Module\Kliken\GoogleAds\Model\Repo\Config {}