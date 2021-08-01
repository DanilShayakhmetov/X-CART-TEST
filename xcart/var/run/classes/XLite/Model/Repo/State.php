<?php
namespace XLite\Model\Repo;
/**
 * Country repository
 *
 * @Api\Operation\Create(modelClass="XLite\Model\State", summary="Add new state")
 * @Api\Operation\Read(modelClass="XLite\Model\State", summary="Retrieve state by id")
 * @Api\Operation\ReadAll(modelClass="XLite\Model\State", summary="Retrieve states by conditions")
 * @Api\Operation\Update(modelClass="XLite\Model\State", summary="Update state by id")
 * @Api\Operation\Delete(modelClass="XLite\Model\State", summary="Delete state by id")
 */
class State extends \XLite\Module\XC\FastLaneCheckout\Model\Repo\State {}