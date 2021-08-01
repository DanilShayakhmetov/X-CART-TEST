<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\RESTAPI\Core\Schema;

/**
 * Complex schema
 */
class Complex extends \XLite\Module\XC\RESTAPI\Core\Schema\Native
{
    use ComplexCompatTrait;

    /**
     * Schema code
     */
    const CODE = 'complex';

    /**
     * Constructor
     *
     * @param \XLite\Core\Request $request Request
     * @param string              $method  Method
     *
     * @return void
     */
    public function __construct(\XLite\Core\Request $request, $method)
    {
        parent::__construct($request, $method);

        $this->modelSchema = $this->buildModelSchema();
    }

    /**
     * Build schema for concrete model
     *
     * @return \XLite\Module\XC\RESTAPI\Core\Schema\Complex\IModel
     * @throws \Exception
     */
    protected function buildModelSchema()
    {
        $classes = $this->getAllowedEntityClasses();
        $className = $this->config->class && isset($classes[$this->config->class])
            ? $classes[$this->config->class]
            : null;

        if (!class_exists($className)) {
            throw new \Exception(
                sprintf("There is no model schema for that class: %s", $this->config->class)
            );
        }

        return new $className();
    }

    /**
     * Check - entity class is allowed or not
     *
     * @param string $class Entity class name
     *
     * @return boolean
     */
    protected function isAllowedEntityClass($class)
    {
        return parent::isAllowedEntityClass($class)
            && array_key_exists($class, $this->getAllowedEntityClasses());
    }

    /**
     * Get allowed entity classes
     *
     * @return array
     */
    protected function getAllowedEntityClasses()
    {
        return array(
            'XLite\Model\Product'   => 'XLite\Module\XC\RESTAPI\Core\Schema\Complex\Product',
            'XLite\Model\Profile'   => 'XLite\Module\XC\RESTAPI\Core\Schema\Complex\Profile',
            'XLite\Model\Order'     => 'XLite\Module\XC\RESTAPI\Core\Schema\Complex\Order',
        );
    }

    /**
     * Check - schema is own this request or not
     *
     * @param string $schema Schema
     *
     * @return boolean
     */
    public static function isOwn($schema)
    {
        return $schema == static::CODE;
    }

    /**
     * Check - valid or not schema
     *
     * @return boolean
     */
    public function isValid()
    {
        return parent::isValid() && $this->isMethodAvailable();
    }

    /**
     * Check - request is forbidden or not
     *
     * @return boolean
     */
    public function isForbid()
    {
        return parent::isForbid()
            || !$this->isMethodAvailable();
    }

    /**
     * @return bool
     */
    protected function isMethodAvailable()
    {
        return in_array($this->config->shortMethod, $this->getAvailableMethods(), true);
    }

    /**
     * @return array
     */
    protected function getAvailableMethods()
    {
        return [ 'get' ];
    }

    /**
     * Get entity class
     *
     * @param string $path Path
     *
     * @return string
     */
    protected function getEntityClass($path)
    {
        $path = strtolower($path);
        if ('person' == $path) {
            $path = 'profile';
        }

        return parent::getEntityClass($path);
    }

    /**
     * Convert model
     *
     * @param mixed   $model            Model OPTIONAL
     * @param boolean $withAssociations Convert with associations OPTIONAL
     *
     * @return mixed
     */
    protected function convertModel($model = null, $withAssociations = true)
    {
        $methodName = 'convertModel' . $this->getModelName();

        return method_exists($this, $methodName) && $this->getModelName()
            ? call_user_func_array(array($this, $methodName), [ $model, $withAssociations ])
            : $this->doConvertModel($model, $withAssociations);
    }

    /**
     * Convert model inner
     *
     * @param mixed   $model            Model OPTIONAL
     * @param boolean $withAssociations Convert with associations OPTIONAL
     *
     * @return mixed
     */
    protected function doConvertModel($model = null, $withAssociations = true)
    {
        return $model
            ? $this->modelSchema->convertModel($model, $withAssociations)
            : null;
    }

    /**
     * Prepare input
     *
     * @param array  $data   Data
     *
     * @return array
     */
    protected function prepareInput(array $data)
    {
        $methodName = 'prepareInput' . $this->getModelName();

        return method_exists($this, $methodName) && $this->getModelName()
            ? call_user_func_array(array($this, $methodName), [ $data ])
            : $this->doPrepareInput($data);
    }

    /**
     * Prepare input
     *
     * @param array  $data   Data
     *
     * @return array
     */
    protected function doPrepareInput(array $data)
    {
        list($checked, $data) = parent::prepareInput($data);

        if ($checked) {
            list($checked, $data) = $this->modelSchema->prepareInput($data);
        }

        return array($checked, $data);
    }

    /**
     * Load data
     *
     * @param \XLite\Model\AEntity $entity Entity
     * @param array                $data   Data
     *
     * @return void
     */
    protected function loadData(\XLite\Model\AEntity $entity, array $data)
    {
        $methodName = 'preload' . $this->getModelName() . 'Data';

        if (method_exists($this, $methodName) && $this->getModelName()) {
            call_user_func_array(array($this, $methodName), [ $entity, $data ]);
        } else {
            $this->doLoadData($entity, $data);
        }
    }

    /**
     * Postprocess entity after flush
     *
     * @param \XLite\Model\AEntity $entity Entity
     * @param array                $data   Data
     *
     * @return void
     */
    protected function postprocessEntity(\XLite\Model\AEntity $entity, array $data)
    {
        parent::postprocessEntity($entity, $data);

        if (method_exists($this->modelSchema, 'postprocessEntity')) {
            $this->modelSchema->postprocessEntity($entity, $data);
        }
    }

    /**
     * Load data
     *
     * @param \XLite\Model\AEntity $entity Entity
     * @param array                $data   Data
     *
     * @return void
     */
    protected function doLoadData(\XLite\Model\AEntity $entity, array $data)
    {
        $this->modelSchema->preloadData($entity, $data);

        parent::loadData($entity, $data);
    }
}
