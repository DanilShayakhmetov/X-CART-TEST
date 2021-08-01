<?php
namespace XLite\Model\Repo;
/**
 * Role repository
 *
 * @Api\Operation\Create(modelClass="XLite\Model\Role", summary="Add new user role")
 * @Api\Operation\Read(modelClass="XLite\Model\Role", summary="Retrieve user role by id")
 * @Api\Operation\ReadAll(modelClass="XLite\Model\Role", summary="Retrieve user roles by conditions")
 * @Api\Operation\Update(modelClass="XLite\Model\Role", summary="Update user role by id")
 * @Api\Operation\Delete(modelClass="XLite\Model\Role", summary="Delete user role by id")
 */
class Role extends \XLite\Module\CDev\UserPermissions\Model\Repo\Role {}