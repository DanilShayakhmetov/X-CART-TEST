<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\RESTAPI\Model;

/**
 * Abstract entity
 */
abstract class AEntity extends \XLite\Model\AEntityAbstract implements \XLite\Base\IDecorator
{

    // {{{ REST API :: get

    /**
     * Build model data for REST API
     *
     * @param boolean $withAssociations Convert with associations OPTIONAL
     *
     * @return mixed
     */
    public function buildDataForREST($withAssociations = true)
    {
        $data = $this->buildFieldsforREST();

        if ($withAssociations) {
            $data += $this->buildAssociationsforREST();
        }

        return $data;
    }

    /**
     * Build plain fields
     *
     * @return array
     */
    protected function buildFieldsforREST()
    {
        $data = array();

        foreach ($this->getModelFieldsForREST() as $name => $field) {
            $data[$name] = $this->getterPropertyForREST($name, $field);
        }

        return $data;
    }

    /**
     * Build associations fields
     *
     * @return array
     */
    protected function buildAssociationsforREST()
    {
        $data = array();

        foreach ($this->getModelAssociationsForREST() as $name => $field) {
            $data[$name] = $this->getterAssociationForREST($name, $field);
        }

        return $data;
    }

    /**
     * Get model fields list for REST API
     *
     * @return array
     */
    protected function getModelFieldsForREST()
    {
        return $this->getRepository()->getPublicClassMetadata()->fieldMappings;
    }

    /**
     * Get model associations list for REST API
     *
     * @return array
     */
    public function getModelAssociationsForREST()
    {
        return $this->getRepository()->getPublicClassMetadata()->associationMappings;
    }

    /**
     * Plain property getter for REST API
     *
     * @param string $name  Field name
     * @param array  $field Field metadata
     *
     * @return string
     */
    protected function getterPropertyForREST($name, array $field)
    {
        $value = $this->getterProperty($name);
        switch ($field['type']) {
            case 'integer':
                $value = intval($value);
                break;

            case 'decimal':
                $value = doubleval($value);
                break;

            case 'binary':
                $value = is_resource($value)
                    ? stream_get_contents($value)
                    : $value;
                break;

            default:
        }

        return $value;
    }

    /**
     * Association getter for REST API
     *
     * @param string $name  Association name
     * @param array  $field Association metadata
     *
     * @return string
     */
    protected function getterAssociationForREST($name, array $field)
    {
        $value = null;

        $association = $this->getterProperty($name);
        if ($association) {
            if ($association instanceOf \XLite\Model\AEntity) {
                $value = $association->buildDataForREST(false);

            } else {
                $value = array();
                foreach ($association as $submodel) {
                    $value[] = $submodel->buildDataForREST(false);
                }
            }
        }

        return $value;
    }

    // }}}

}
