<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\SilexAnnotations\ServiceAnnotation;

use Pimple\Exception\UnknownIdentifierException;
use ReflectionMethod;
use Silex\Application;
use XCart\SilexAnnotations\NameConverter\INameConverter;

class ArgumentsMapper
{
    /**
     * @var Application
     */
    private $app;

    /**
     * @var array
     */
    private $mappings;

    /**
     * @var INameConverter
     */
    private $nameConverter;

    /**
     * @param Application    $app
     * @param INameConverter $nameConverter
     * @param array          $mappings
     */
    public function __construct(Application $app, INameConverter $nameConverter, array $mappings)
    {
        $this->app           = $app;
        $this->nameConverter = $nameConverter;
        $this->mappings      = [$this->nameConverter->classNameToServiceName(Application::class) => $app] + $mappings;
    }

    /**
     * @param ReflectionMethod $method
     * @param array            $mappings
     *
     * @return array
     */
    public function getArguments(ReflectionMethod $method, array $mappings = [])
    {
        $result = [];

        foreach ($method->getParameters() as $parameter) {
            $typeClass = $this->getParameterTypeClass($parameter);
            if ($typeClass
                && !isset($mappings[$parameter->getName()])
            ) {
                $serviceName = $this->nameConverter->classNameToServiceName($typeClass);
                $result[]    = ['service' => $serviceName];

            } elseif (isset($mappings[$parameter->getName()])) {
                $result[] = ['service' => $mappings[$parameter->getName()]];

            } elseif ($parameter->isDefaultValueAvailable()) {
                $result[] = ['value' => $parameter->getDefaultValue()];

            } else {
                $result[] = ['service' => $parameter->getName()];
            }
        }

        return $result;
    }

    /**
     * @param array $arguments
     *
     * @return array
     */
    public function getValues(array $arguments)
    {
        return array_map([$this, 'getValue'], $arguments);
    }

    /**
     * @param array $argument
     *
     * @return mixed
     * @throws UnknownIdentifierException
     */
    private function getValue($argument)
    {
        if (isset($argument['value'])) {

            return $argument['value'];
        }

        $service = isset($argument['service']) ? $argument['service'] : null;

        if (isset($this->mappings[$service])) {
            $service = $this->mappings[$service];
        }

        return (is_string($service) && $this->app->offsetExists($service))
            ? $this->app->offsetGet($service)
            : $service;
    }

    /**
     * @param \ReflectionParameter $parameter
     *
     * @return string
     */
    private function getParameterTypeClass($parameter)
    {
        if (preg_match('/\[\s\<\w+?>\s([\w\\\\]+)/S', (string) $parameter, $matches)) {
            if (!empty($matches[1]) && !$this->isScalarType($matches[1])) {

                return $matches[1];
            }
        }

        return '';
    }

    /**
     * @param string $type
     *
     * @return boolean
     */
    private function isScalarType($type)
    {
        return in_array(
            $type,
            [
                'boolean',
                'integer',
                'double',
                'string',
                'array',
                'object',
                'resource',
                'NULL',
                'unknown type',
            ],
            true
        );
    }
}
