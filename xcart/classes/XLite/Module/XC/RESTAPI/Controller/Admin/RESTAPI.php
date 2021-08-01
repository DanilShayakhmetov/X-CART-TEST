<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\RESTAPI\Controller\Admin;

use XLite\Module\XC\RESTAPI\Core\Exception\IncorrectInputData;
use XLite\Module\XC\RESTAPI\Core\Exception\IncorrectInputType;
use XLite\Module\XC\RESTAPI\Core\InputDataMapper;

/**
 * REST API controller
 */
class RESTAPI extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Allowed REST methods
     *
     * @var array
     */
    protected $allowedMethods = array('GET', 'POST', 'PUT', 'DELETE');

    /**
     * Cached method
     *
     * @var string
     */
    protected $cachedMethod;

    /**
     * Check if current page is accessible
     *
     * @return boolean
     */
    public function checkAccess()
    {
        return parent::checkAccess()
            && $this->isRESTRequestAllowed()
            && $this->getSchema()
            && $this->getSchema()->isValid()
            && $this->getPrinter();
    }

    /**
     * Check - REST request is allowed or not
     *
     * @return boolean
     */
    protected function isRESTRequestAllowed()
    {
        $authService = new \XLite\Module\XC\RESTAPI\Core\Auth\Keys(
            \XLite\Core\Config::getInstance()->XC->RESTAPI->key,
            \XLite\Core\Config::getInstance()->XC->RESTAPI->key_read
        );

        return $this->isWriteMethod()
            ? $authService->allowWrite(\XLite\Core\Request::getInstance()->_key)
            : $authService->allowRead(\XLite\Core\Request::getInstance()->_key);
    }

    /**
     * Check - is write method or not
     * 
     * @return boolean
     */
    protected function isWriteMethod()
    {
        return in_array($this->getMethod(), array('POST', 'PUT', 'DELETE'));
    }

    /**
     * Check - is current place public or not
     *
     * @return boolean
     */
    protected function isPublicZone()
    {
        return true;
    }

    /**
     * Process request
     *
     * @return void
     */
    public function processRequest()
    {
    }

    /**
     * Mark controller run thread as access denied
     *
     * @return void
     */
    protected function markAsAccessDenied()
    {
        if ($this->getSchema() && $this->getSchema()->isForbid()) {
            header('HTTP/1.0 403 Forbidden', true, 403);

        } else {
            header('HTTP/1.0 404 Not Found', true, 404);
        }

        $this->setSuppressOutput(true);
        $this->silence = true;
    }

    /**
     * Handles the request.
     * Parses the request variables if necessary. Attempts to call the specified action function
     *
     * @return void
     */
    public function handleRequest()
    {
        try {
            if ($this->isWriteMethod()) {
                $this->mapPayloadIntoRequest();
            }
            $this->set('silent', true);

            parent::handleRequest();
    
        } catch (IncorrectInputType $e) {
            header('HTTP/1.0 400 Bad Request', true, 400);
            header('X-REST-Error: Unknown Content-Type provided');
        } catch (IncorrectInputData $e) {
            header('HTTP/1.0 400 Bad Request', true, 400);
            header('X-REST-Error: Input data is incorrect');
        }
    }

    /**
     * @return bool
     */
    protected function isMultipleRequest()
    {
        $result = false;

        $path = \XLite\Core\Request::getInstance()->_path;
        if ($path) {
            $parts = explode('/', $path);
            $result = !isset($parts[1]);
        }

        return $result;
    }

    /**
     * Process PUT request
     *
     * @return void
     * @throws \XLite\Module\XC\RESTAPI\Core\Exception\IncorrectInputType
     */
    protected function mapPayloadIntoRequest()
    {
        $rawData = '';
        $s = fopen('php://input', 'rb');
        while ($kb = fread($s, 1024)) {
            $rawData .= $kb;
        }
        fclose($s);

        $mapper = new InputDataMapper();

        $parsedArray = $mapper->getMapped(
            $rawData,
            $this->getContentType(),
            $this->isMultipleRequest()
        );

        if ($parsedArray) {
            \XLite\Core\Request::getInstance()->mapRequest($parsedArray);
        }
    }

    /**
     * @return string
     */
    protected function getContentType()
    {
        return !empty($_SERVER["CONTENT_TYPE"])
            ? $_SERVER["CONTENT_TYPE"]
            : 'application/x-www-form-urlencoded';
    }

    /**
     * Preprocessor for no-action run
     *
     * @return void
     */
    protected function doNoAction()
    {
        try {
            $data = $this->getSchema()->process();
            $this->getContainer()->get('widget_cache_manager')->invalidateBasedOnDatabaseChanges();

        } catch (\Exception $e) {
            header('HTTP/1.0 400 Bad Request', true, 400);
            header('X-REST-Error: ' . str_replace(["\n","\r"], '', $e->getMessage()));
            $data = [];
        }

        $this->getPrinter()->printOutput($data);
    }

    /**
     * Get printer
     *
     * @return \XLite\Module\XC\RESTAPI\Core\Printer\APrinter
     */
    protected function getPrinter()
    {
        if (!isset($this->printer)) {
            $this->printer = \XLite\Module\XC\RESTAPI\Core\PrinterFactory::create(
                $this->getSchema()
            );
        }

        return $this->printer;
    }

    /**
     * Get schema
     *
     * @return \XLite\Module\XC\RESTAPI\Core\Schema\ASchema
     */
    public function getSchema()
    {
        if (!isset($this->schema)
            || !($this->schema instanceof \XLite\Module\XC\RESTAPI\Core\Schema\ASchema)
        ) {
            $request = \XLite\Core\Request::getInstance();

            $this->schema = \XLite\Module\XC\RESTAPI\Core\SchemaFactory::create(
                $request->_schema,
                $request,
                $this->getMethod()
            );
        }

        return $this->schema;
    }
    /**
     * Get method
     *
     * @return string
     */
    protected function getMethod()
    {
        if (!isset($this->cachedMethod)) {
            if (!empty(\XLite\Core\Request::getInstance()->_method)) {
                $this->cachedMethod = strtoupper(trim(\XLite\Core\Request::getInstance()->_method));

            } elseif (!empty($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'])) {
                $this->cachedMethod = strtoupper($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE']);

            } else {
                $this->cachedMethod = strtoupper(\XLite\Core\Request::getInstance()->getRequestMethod());
            }

            if (!in_array($this->cachedMethod, $this->allowedMethods)) {
                $this->cachedMethod = $this->allowedMethods[0];
            }
        }

        return $this->cachedMethod;
    }
}

