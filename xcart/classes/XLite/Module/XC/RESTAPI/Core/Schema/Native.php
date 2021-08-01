<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\RESTAPI\Core\Schema;

use Doctrine\ORM\Internal\Hydration\IterableResult;
use Includes\Utils\Module\Manager;

/**
 * Native schema
 */
class Native extends \XLite\Module\XC\RESTAPI\Core\Schema\ASchema
{
    /**
     * Schema code
     */
    const CODE = 'default';

    /**
     * Check - schema is own this request or not
     *
     * @param string $schema Schema
     *
     * @return boolean
     */
    public static function isOwn($schema)
    {
        return !$schema || parent::isOwn($schema);
    }

    // {{{ GET

    /**
     * Find data for getAll request
     *
     * @return mixed
     */
    protected function findForGetAll()
    {
        /** @var \XLite\Model\Repo\ARepo $repository */
        $repository = $this->config->repository;
        $cnd = $this->preprocessCnd($this->config->cnd);

        /** @var IterableResult $result */
        $result = iterator_count($cnd)
            ? $repository->search($cnd, 'restapi')
            : $repository->findAllForREST();

        return $result;
    }

    /**
     * Get list of cnd proprocess handlers
     *
     * @return array
     */
    protected static function getPreprocessHandlers()
    {
        return array(
            'dateRange' => 'preprocessDateRange',
        );
    }

    /**
     * Get cnd proprocess handler
     *
     * @param string $name Cnd name
     *
     * @return callable|null
     */
    protected function getPreprocessHandler($name)
    {
        $list = static::getPreprocessHandlers();

        return isset($list[$name])
            ? array($this, $list[$name])
            : null;
    }

    /**
     * Cnd dateRange preprocessor
     *
     * @param string $value Value of condition
     *
     * @return array
     */
    protected function preprocessDateRange($value)
    {
        $value = trim($value);

        if (is_string($value) && $value) {
            $value = \XLite\View\FormField\Input\Text\DateRange::convertToArray($value);
        }

        return $value;
    }

    /**
     * Preprocess cnd
     *
     * @param \XLite\Core\CommonCell $cnd Cnd
     *
     * @return \XLite\Core\CommonCell
     */
    protected function preprocessCnd(\XLite\Core\CommonCell $cnd)
    {
        foreach ($cnd as $key => $value) {
            $handler = $this->getPreprocessHandler($key);

            if ($handler) {
                $cnd->{$key} = call_user_func_array($handler, array($value));
            }
        }
        return $cnd;
    }

    /**
     * Find data for getOne request
     *
     * @return \XLite\Model\AEntity
     */
    protected function findForGetOne()
    {
        return $this->config->id
            ? $this->config->repository->findOneForREST($this->config->id)
            : null;
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
    protected function findForPutOne($id)
    {
        return $this->config->repository->findOneForREST($id);
    }

    // }}}

    // {{{ DELETE

    /**
     * Find data for deleteAll request
     *
     * @return mixed
     */
    protected function findForDeleteAll()
    {
        return $this->config->repository->findAllForREST();
    }

    /**
     * Find data for deleteOne request
     *
     * @param mixed $id Id
     *
     * @return \XLite\Model\AEntity
     */
    protected function findForDeleteOne($id)
    {
        return $this->config->repository->findOneForREST($id);
    }

    // }}}

    // {{{ Common routines

    /**
     * Detect entity class
     *
     * @return string
     */
    protected function detectEntityClass()
    {
        return $this->config->class;
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
        return $model ? $model->buildDataForREST($withAssociations) : null;
    }

    /**
     * Assemble repository posprocess method name
     *
     * @param string $method Method name
     *
     * @return string
     */
    protected function assembleRepoPosprocessMethodName($method)
    {
        return 'postprocess' . ucfirst($method) . 'RESTRequest';
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
    protected function getEntityClass($path)
    {
        $parts = array_map('ucfirst', explode('-', strtolower($path)));

        $path = $this->normalizeFilePath('XLite' . LC_DS . 'Model' . LC_DS . implode(LC_DS, $parts) . '.php');
        $class = $path ? str_replace(LC_DS, '\\', substr($path, 0, -4)) : null;
        if (!$path || !\XLite\Core\Operator::isClassExists($class)) {
            $class = null;
        }

        if (!$class && 2 < count($parts)) {
            $modules = Manager::getRegistry()->getModules();
            $module = array_filter(
                $modules,
                function ($moduleName) use ($parts) {
                    return strtolower($moduleName) === strtolower($parts[0] . '-' . $parts[1]);
                },
                ARRAY_FILTER_USE_KEY
            );
            if ($module) {
                $moduleKeys = array_keys($module);
                list($parts[0], $parts[1]) = explode('-', reset($moduleKeys));
            }

            if (Manager::getRegistry()->isModuleEnabled($parts[0], $parts[1])) {
                $path = $this->normalizeFilePath(
                    'XLite' . LC_DS . 'Module' . LC_DS
                    . $parts[0] . LC_DS . $parts[1] . LC_DS . 'Model'
                    . LC_DS . implode(LC_DS, array_slice($parts, 2)) . '.php'
                );
                $class = $path ? str_replace(LC_DS, '\\', substr($path, 0, -4)) : null;
                if (!$path || !\XLite\Core\Operator::isClassExists($class)) {
                    $class = null;
                }
            }
        }

        return $class && $this->isAllowedEntityClass($class) ? $class : null;
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
        return true;
    }

    /**
     * Normalize file path
     *
     * @param string $relPath Relative file path
     *
     * @return string
     */
    protected function normalizeFilePath($relPath)
    {
        $baseDir = LC_DIR_CACHE_CLASSES;
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($baseDir),
            \RecursiveIteratorIterator::LEAVES_ONLY,
            \FilesystemIterator::SKIP_DOTS
        );

        $baseLength = strlen($baseDir);
        $lowPath = strtolower($relPath);
        $result = null;
        foreach ($iterator as $path => $file) {
            $path = substr($path, $baseLength);
            if (strtolower($path) == $lowPath) {
                $result = $path;
                break;
            }
        }

        return $result;
    }

    // }}}

}
