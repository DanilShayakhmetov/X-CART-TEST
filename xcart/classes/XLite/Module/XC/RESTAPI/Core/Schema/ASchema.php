<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\RESTAPI\Core\Schema;

/**
 * Abstract schema
 */
abstract class ASchema extends \XLite\Base
{
    /**
     * Schema code
     */
    const CODE = null;

    /**
     * Config
     *
     * @var   \ArrayObject
     */
    protected $config;

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
        $this->config = new \ArrayObject($this->configure($request, $method), \ArrayObject::ARRAY_AS_PROPS);
    }

    // {{{ Initialization

    /**
     * Check - schema is own this request or not
     *
     * @param string $schema Schema
     *
     * @return boolean
     */
    public static function isOwn($schema)
    {
        return trim(strtolower($schema)) == strtolower(static::CODE);
    }

    /**
     * Get config
     *
     * @return \ArrayObject
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Check - valid or not schema
     *
     * @return boolean
     */
    public function isValid()
    {
        return (bool) $this->config->repository;
    }

    // }}}

    // {{{ Comomn validation and access control

    /**
     * Check - request is forbidden or not
     *
     * @return boolean
     */
    public function isForbid()
    {
        return (bool) $this->config->repository;
    }

    /**
     * Process
     *
     * @return array
     */
    public function process()
    {
        $method = 'process' . ucfirst($this->config->method) . 'RESTRequest';

        return $this->config->repository->processRESTRequest(
            $this->config->method,
            $this->$method()
        );
    }

    // }}}

    // {{{ Process

    /**
     * Configure
     *
     * @param \XLite\Core\Request $request Request
     * @param string              $method  Method
     *
     * @return array
     */
    protected function configure(\XLite\Core\Request $request, $method)
    {
        $parts = explode('/', $this->getPath($request));
        $one   = isset($parts[1]);
        $class = $this->getEntityClass($parts[0]);

        return [
            'shortMethod' => strtolower($method),
            'method'      => strtolower($method) . ($one ? 'One' : 'All'),
            'one'         => $one,
            'request'     => $request,
            'multiple'    => !$one,
            'id'          => isset($parts[1]) ? trim($parts[1]) : null,
            'class'       => $class,
            'repository'  => $this->getRepository($class),
            'cnd'         => $this->getCndFromRequest($request),
        ];
    }

    // }}}

    // {{{ Get

    /**
     * Find data for getAll request
     *
     * @return mixed
     */
    abstract protected function findForGetAll();

    /**
     * Find data for getOne request
     *
     * @return \XLite\Model\AEntity
     */
    abstract protected function findForGetOne();

    /**
     * Process getAll REST request
     *
     * @return array
     */
    protected function processGetAllRESTRequest()
    {
        $result = [];

        foreach ($this->findForGetAll() as $model) {
            $model    = is_array($model) ? $model[0] : $model;
            $result[] = $this->convertModelForGetAll($model);
        }

        return $result;
    }

    /**
     * Process getOne REST request
     *
     * @return array
     */
    protected function processGetOneRESTRequest()
    {
        return $this->convertModel($this->findForGetOne());
    }

    /**
     * Convert model for getAll
     *
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return array
     */
    protected function convertModelForGetAll(\XLite\Model\AEntity $entity)
    {
        return $this->convertModel($entity, false);
    }

    /**
     * Convert model for getOne
     *
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return array
     */
    protected function convertModelForGetOne(\XLite\Model\AEntity $entity)
    {
        return $this->convertModel($entity);
    }

    // }}}

    // {{{ POST

    /**
     * Process postAll REST request
     *
     * @return array
     */
    protected function processPostAllRESTRequest()
    {
        $response = [];

        foreach ($this->getInput() as $id => $row) {
            list($checked, $data) = $this->prepareInput($row);
            if ($checked) {
                $entity = $this->createEntity();
                $this->loadData($entity, $data);
                $response[$id] = $entity;
            }
        }

        \XLite\Core\Database::getEM()->flush();

        $this->callPostprocessMethod();

        foreach ($response as $id => $entity) {
            $response[$id] = $this->convertModelForPostAll($entity);
        }

        return $response;
    }

    /**
     * Process postOne REST request
     *
     * @return array
     */
    protected function processPostOneRESTRequest()
    {
        $response = null;

        list($checked, $data) = $this->prepareInput($this->getInput());

        if ($checked) {
            $entity = $this->createEntity();

            \Xlite\Core\Database::getRepo('XLite\Model\Product')->setBlockQuickDataFlag(true);
            $this->loadData($entity, $data);
            \XLite\Core\Database::getEM()->flush();

            $this->postprocessEntity($entity, $data);

            $this->callPostprocessMethod();

            $response = $this->convertModelForPostOne($entity);
        }

        return $response;
    }

    /**
     * Convert model for postAll
     *
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return array
     */
    protected function convertModelForPostAll(\XLite\Model\AEntity $entity)
    {
        return $this->convertModel($entity, false);
    }

    /**
     * Convert model for postOne
     *
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return array
     */
    protected function convertModelForPostOne(\XLite\Model\AEntity $entity)
    {
        return $this->convertModel($entity);
    }

    // }}}

    // {{{ PUT

    /**
     * Find data for putOne request
     *
     * @param mixed $id Id
     *
     * @return \XLite\Model\AEntity
     */
    abstract protected function findForPutOne($id);

    /**
     * Process putAll REST request
     *
     * @return array
     */
    protected function processPutAllRESTRequest()
    {
        $response = [];

        foreach ($this->getInput() as $id => $row) {
            list($checked, $data) = $this->prepareInput($row);
            if ($checked) {
                $idName = $this->config->repository->getPrimaryKeyField();
                if (!empty($data[$idName])) {
                    $entity = $this->findForPutOne($data[$idName]);
                    if ($entity) {
                        $this->loadData($entity, $data);
                        $response[$id] = $entity;
                    }
                }
            }
        }

        \XLite\Core\Database::getEM()->flush();

        $this->callPostprocessMethod();

        foreach ($response as $id => $entity) {
            $response[$id] = $this->convertModelForPutAll($entity);
        }

        return $response;
    }

    /**
     * Process putOne REST request
     *
     * @return array
     */
    protected function processPutOneRESTRequest()
    {
        $response = null;

        list($checked, $data) = $this->prepareInput($this->getInput());
        if ($checked) {
            $entity = $this->findForPutOne($this->config->id);
            if ($entity) {
                $this->loadData($entity, $data);
                \XLite\Core\Database::getEM()->flush();

                $this->callPostprocessMethod();

                $response = $this->convertModelForPutOne($entity);
            }
        }

        return $response;
    }

    /**
     * Convert model for putAll
     *
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return array
     */
    protected function convertModelForPutAll(\XLite\Model\AEntity $entity)
    {
        return $this->convertModel($entity, false);
    }

    /**
     * Convert model for putOne
     *
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return array
     */
    protected function convertModelForPutOne(\XLite\Model\AEntity $entity)
    {
        return $this->convertModel($entity);
    }

    // }}}

    // {{{ DELETE

    /**
     * Find data for deleteAll request
     *
     * @return mixed
     */
    abstract protected function findForDeleteAll();

    /**
     * Find data for deleteOne request
     *
     * @param mixed $id Id
     *
     * @return \XLite\Model\AEntity
     */
    abstract protected function findForDeleteOne($id);

    /**
     * Process deleteAll REST request
     *
     * @return integer
     */
    protected function processDeleteAllRESTRequest()
    {
        $i = 0;

        foreach ($this->findForDeleteAll() as $entity) {
            $entity = is_array($entity) ? $entity[0] : $entity;
            \XLite\Core\Database::getEM()->remove($entity);
            $i++;
        }

        \XLite\Core\Database::getEM()->flush();

        $this->callPostprocessMethod();

        return $i;
    }

    /**
     * Process deleteOne REST request
     *
     * @return array
     */
    protected function processDeleteOneRESTRequest()
    {
        $response = null;

        $entity = $this->findForDeleteOne($this->config->id);
        if ($entity) {
            \XLite\Core\Database::getEM()->remove($entity);
            \XLite\Core\Database::getEM()->flush();
            $this->callPostprocessMethod();
            $response = true;
        }

        return $response;
    }

    // }}}

    // {{{ Common routines

    /**
     * Detect entity class
     *
     * @return string
     */
    abstract protected function detectEntityClass();

    /**
     * Convert model
     *
     * @param mixed   $model            Model OPTIONAL
     * @param boolean $withAssociations Convert with associations OPTIONAL
     *
     * @return mixed
     */
    abstract protected function convertModel($model = null, $withAssociations = true);

    /**
     * Assemble repository posprocess method name
     *
     * @param string $method Method name
     *
     * @return string
     */
    abstract protected function assembleRepoPosprocessMethodName($method);

    /**
     * Create entity
     *
     * @return \XLite\Model\AEntity
     */
    protected function createEntity()
    {
        $class = $this->detectEntityClass();

        return new $class;
    }

    /**
     * Call postprocess method
     *
     * @param string $method Method name OPTIONAL
     *
     * @return void
     */
    protected function callPostprocessMethod($method = null)
    {
        if (!$method) {
            $method = $this->config->shortMethod;
        }

        $method = $this->assembleRepoPosprocessMethodName($method);
        if (method_exists($this->config->repository, $method)) {
            $this->config->repository->{$method}();
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
    protected function loadData(\XLite\Model\AEntity $entity, array $data)
    {
        $this->config->repository->loadRawFixture($entity, $data);
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
    }

    // }}}

    // {{{ Input

    /**
     * Get input
     *
     * @return array
     */
    protected function getInput()
    {
        $data = null;

        $requestData = $this->config->request->getNonFilteredData();

        if (!empty($requestData['model']) && is_array($requestData['model'])) {
            $data = $requestData['model'];

        } else {
            $data = $requestData;
            foreach ($this->getServiceInputKeys() as $key) {
                if (isset($data[$key])) {
                    unset($data[$key]);
                }
            }
        }

        return $data;
    }

    /**
     * Get service input keys
     *
     * @return array
     */
    protected function getServiceInputKeys()
    {
        return ['target', 'action', '_key', '_path', '_method', 'callback', '_schema'];
    }

    /**
     * Prepare input
     *
     * @param array $data Data
     *
     * @return array
     */
    protected function prepareInput(array $data)
    {
        $method = 'prepareInputFor' . ucfirst($this->config->method);

        return $this->$method($this->filterInput($data, $method));
    }

    /**
     * Prepare input for getOne
     *
     * @param array $data Data
     *
     * @return array
     */
    protected function prepareInputForGetOne(array $data)
    {
        return [true, $data];
    }

    /**
     * Prepare input for getAll
     *
     * @param array $data Data
     *
     * @return array
     */
    protected function prepareInputForGetAll(array $data)
    {
        return [true, $data];
    }

    /**
     * Prepare input for postOne
     *
     * @param array $data Data
     *
     * @return array
     */
    protected function prepareInputForPostOne(array $data)
    {
        return [true, $data];
    }

    /**
     * Prepare input for postAll
     *
     * @param array $data Data
     *
     * @return array
     */
    protected function prepareInputForPostAll(array $data)
    {
        return [true, $data];
    }

    /**
     * Prepare input for putOne
     *
     * @param array $data Data
     *
     * @return array
     */
    protected function prepareInputForPutOne(array $data)
    {
        return [true, $data];
    }

    /**
     * Prepare input for putAll
     *
     * @param array $data Data
     *
     * @return array
     */
    protected function prepareInputForPutAll(array $data)
    {
        return [true, $data];
    }

    /**
     * Filter input
     *
     * @param array  $data   Data
     * @param string $method Method name
     *
     * @return array
     */
    protected function filterInput(array $data, $method)
    {
        $method = 'getFilterKeysFor' . ucfirst($method);

        if (method_exists($this, $method)) {
            $data = array_intersect_key($data, array_flip($this->$method()));
        }

        return $data;
    }

    // }}}

    // {{{ Utils

    /**
     * Get entity class
     *
     * @param string $path Path
     *
     * @return string
     */
    abstract protected function getEntityClass($path);

    /**
     * Get path
     *
     * @param \XLite\Core\Request $request Request
     *
     * @return string
     */
    protected function getPath(\XLite\Core\Request $request)
    {
        return $request->_path;
    }

    /**
     * Get repository
     *
     * @param string $class Class
     *
     * @return \XLite\Model\Repo\ARepo
     */
    protected function getRepository($class)
    {
        return $class ? \XLite\Core\Database::getRepo($class) : null;
    }

    /**
     * Get condition from request
     *
     * @param \XLite\Core\Request $request Request
     *
     * @return array
     */
    protected function getCndFromRequest(\XLite\Core\Request $request)
    {
        $condition = [];

        foreach ($request->_cnd ?: [] as $key => $value) {
            $condition[is_string($key) ? trim($key) : $key] = $value;
        }

        return new \XLite\Core\CommonCell($condition);
    }

    // }}}

}
