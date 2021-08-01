<?php
namespace XLite\Model\Repo;
/**
 * The Profile model repository
 *
 * @Api\Operation\Create(modelClass="XLite\Model\Profile", summary="Add new profile")
 * @Api\Operation\Read(modelClass="XLite\Model\Profile", summary="Retrieve profile by id")
 * @Api\Operation\ReadAll(modelClass="XLite\Model\Profile", summary="Retrieve profiles by conditions")
 * @Api\Operation\Update(modelClass="XLite\Model\Profile", summary="Update profile by id")
 * @Api\Operation\Delete(modelClass="XLite\Model\Profile", summary="Delete profile by id")
 */
class Profile extends \XLite\Module\XC\MailChimp\Model\Repo\Profile {}