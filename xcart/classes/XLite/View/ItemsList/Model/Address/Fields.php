<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ItemsList\Model\Address;

use Includes\Utils\Module\Manager;
use Includes\Utils\Module\Module;
use XLite\Model\AddressField;

/**
 * Address fields items list
 */
class Fields extends \XLite\View\ItemsList\Model\Table
{
    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();

        $list[] = 'address_fields';

        return $list;
    }

    /**
     * Should itemsList be wrapped with form
     *
     * @return boolean
     */
    protected function wrapWithFormByDefault()
    {
        return true;
    }

    /**
     * Get wrapper form target
     *
     * @return array
     */
    protected function getFormTarget()
    {
        return 'address_fields';
    }

    /**
     * Get a list of CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = $this->getDir() . '/' . $this->getPageBodyDir() . '/address_fields/style.css';
        $list[] = 'address/fields/style.css';

        return $list;
    }

    /**
     * @param array                $column
     * @param \XLite\Model\AEntity $model
     */
    public function getHelpText(array $column, \XLite\Model\AEntity $model)
    {
        return static::t('Required state_id checkbox help text');
    }

    /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
        return array(
            'name' => array(
                static::COLUMN_NAME     => static::t('Name'),
                static::COLUMN_CLASS    => 'XLite\View\FormField\Inline\Input\Text',
                static::COLUMN_PARAMS   => [
                    'required' => true,
                    \XLite\View\FormField\Input\Base\StringInput::PARAM_MAX_LENGTH => 30,
                ],
                static::COLUMN_ORDERBY  => 100,
            ),
            'serviceName' => array(
                static::COLUMN_NAME     => static::t('Service name'),
                static::COLUMN_CLASS    => 'XLite\View\FormField\Inline\Input\AddressFieldsServiceName',
                static::COLUMN_TEMPLATE => 'items_list/model/table/field.twig',
                static::COLUMN_PARAMS   => array('required' => true),
                static::COLUMN_ORDERBY  => 200,
            ),
            'required' => array(
                static::COLUMN_NAME     => static::t('Required'),
                static::COLUMN_CLASS    => 'XLite\View\FormField\Inline\Input\Checkbox\Switcher\YesNo',
                static::COLUMN_TEMPLATE => 'address/fields/required.help.twig',
                static::COLUMN_PARAMS   => array(),
                static::COLUMN_ORDERBY  => 300,
            ),
        );
    }

    /**
     * Define repository name
     *
     * @return string
     */
    protected function defineRepositoryName()
    {
        return 'XLite\Model\AddressField';
    }

    // {{{ Behaviors

    /**
     * Mark list as removable
     *
     * @return boolean
     */
    protected function isRemoved()
    {
        $found = false;
        foreach ($this->getPageData() as $model) {
            if ($model->getAdditional()) {
                $found = true;
                break;
            }
        }

        return $found;
    }

    /**
     * Mark list as switchable (enable / disable)
     *
     * @return boolean
     */
    protected function isSwitchable()
    {
        return true;
    }

    /**
     * Template for switcher action definition
     *
     * @return string
     */
    protected function getSwitcherActionTemplate()
    {
        return 'items_list/model/table/address_fields/switcher.twig';
    }

    /**
     * Mark list as sortable
     *
     * @return integer
     */
    protected function getSortableType()
    {
        return static::SORT_TYPE_MOVE;
    }

    /**
     * Get create entity URL
     *
     * @return string
     */
    protected function getCreateURL()
    {
        return \XLite\Core\Converter::buildURL('address_field');
    }

    /**
     * Get create button label
     *
     * @return string
     */
    protected function getCreateButtonLabel()
    {
        return 'New address field';
    }

    /**
     * Creation button position
     *
     * @return integer
     */
    protected function isInlineCreation()
    {
        return static::CREATE_INLINE_TOP;
    }

    // }}}

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' address-fields';
    }

    /**
     * Return params list to use for search
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchCondition()
    {
        $result = parent::getSearchCondition();

        $result->{\XLite\Model\Repo\AddressField::CND_WITHOUT_CSTATE} = true;

        return $result;
    }

    /**
     * Return "empty list" catalog
     *
     * @return string
     */
    protected function getEmptyListDir()
    {
        return parent::getEmptyListDir();
    }

    /**
     * Check - remove entity or not
     *
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return boolean
     */
    protected function isAllowEntityRemove(\XLite\Model\AEntity $entity)
    {
        return parent::isAllowEntityRemove($entity) && $entity->getAdditional();
    }

    /**
     * Check - switch entity or not
     *
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return boolean
     */
    protected function isAllowEntitySwitch(\XLite\Model\AEntity $entity)
    {
        /* @var AddressField $entity */
        // Custom state is not allowed to switch off
        return parent::isAllowEntitySwitch($entity)
            && 'custom_state' !== $entity->getServiceName()
            && class_exists($entity->getSchemaClass());
    }

    /**
     * @param \XLite\Model\AEntity $entity
     *
     * @return boolean
     */
    protected function isShowSwitchWarning(\XLite\Model\AEntity $entity)
    {
        return !class_exists($entity->getSchemaClass());
    }

    /**
     * @param \XLite\Model\AEntity $entity
     *
     * @return string
     */
    protected function getSwitchWarningMessage(\XLite\Model\AEntity $entity)
    {
        $schemaClass = $entity->getSchemaClass();
        $moduleId = Module::getModuleIdByClassName($schemaClass);

        if (!$moduleId) {
            return static::t('The field can not be enabled');
        }

        $module = Manager::getRegistry()->getModule($moduleId);

        if ($module) {
            return static::t(
                'The field can not be enabled since the X addon is disabled.',
                [
                    'name' => $module->moduleName,
                    'link' => Manager::getRegistry()->getModuleServiceURL($moduleId),
                ]
            );
        }

        return static::t('The field can not be enabled since the corresponding addon is disabled.');
    }

    /**
     * Check if the column template is used for widget displaying
     *
     * @param array                $column Column
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return boolean
     */
    protected function isTemplateColumnVisible(array $column, \XLite\Model\AEntity $entity)
    {
        $result = null;

        if ('serviceName' === $column[static::COLUMN_CODE]) {
            // Right now admin cannot directly edit serviceName values for additional fields
            // and cannot change "Not required" state of "custom_state" field
            // TODO: refactor it
            $result = !$entity->getAdditional();
        } elseif('required' === $column[static::COLUMN_CODE]) {
            $result = $entity->getServiceName() === 'state_id';
        }

        return $result !== null
            ? $result
            : parent::isTemplateColumnVisible($column, $entity);
    }


    /**
     * Check if the simple class is used for widget displaying
     *
     * @param array                $column Column
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return boolean
     */
    protected function isClassColumnVisible(array $column, \XLite\Model\AEntity $entity)
    {
        $result = null;

        if ('serviceName' === $column[static::COLUMN_CODE]) {
            // Right now admin cannot directly edit serviceName values for additional fields
            // and cannot change "Not required" state of "custom_state" field
            // TODO: refactor it
            $result = $entity->getAdditional();
        } elseif('required' === $column[static::COLUMN_CODE]) {
            return !in_array($entity->getServiceName(), ['custom_state', 'state_id'], true);
        }

        return $result !== null
            ? $result
            : parent::isClassColumnVisible($column, $entity);
    }

    /**
     * Update entities
     *
     * @return void
     */
    protected function updateEntities()
    {
        parent::updateEntities();

        $enabled = false;
        $name = 'State';
        $custom_state_position = 0;
        foreach ($this->getPageData() as $entity) {
            if ('state_id' == $entity->getServiceName()) {
                $enabled = $entity->getEnabled();
                $name = $entity->getName();
                $custom_state_position = $entity->getPosition();
            }
        }

        $entity = \XLite\Core\Database::getRepo('XLite\Model\AddressField')->findOneByServiceName('custom_state');
        if ($entity) {
            $entity->setEnabled($enabled);
            $entity->setName($name);
            $entity->setPosition($custom_state_position);
        }

    }

    /**
     * Get forbidden values for service_name field
     *
     * @return array
     */
    protected function getForbiddenServiceNames()
    {
        return array('email');
    }

    /**
     * Post-validate new entity
     *
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return boolean
     */
    protected function prevalidateNewEntity(\XLite\Model\AEntity $entity)
    {
        $result = parent::prevalidateNewEntity($entity);

        if (in_array($entity->getServiceName(), $this->getForbiddenServiceNames())) {
            $result = false;
            $this->errorMessages[] = static::t(
                'The service name X is reserved and cannot be used for an address field.',
                array('value' => $entity->getServiceName())
            );
        }

        return $result;
    }

    /**
     * Post-validate new entity
     *
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return boolean
     */
    protected function prevalidateEntity(\XLite\Model\AEntity $entity)
    {
        $result = parent::prevalidateNewEntity($entity);

        if (in_array($entity->getServiceName(), $this->getForbiddenServiceNames())) {
            $result = false;
            $this->errorMessages[] = static::t(
                'The service name X is reserved and cannot be used for an address field.',
                array('value' => $entity->getServiceName())
            );
        }

        return $result;
    }
}
