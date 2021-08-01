<?php

namespace XCart\SilexAnnotationsTest\ServiceAnnotation;

use PHPUnit\Framework\TestCase;
use Silex\Application;
use XCart\SilexAnnotations\ServiceAnnotation\ArgumentsMapper;
use XCart\SilexAnnotations\NameConverter\DotNotation;

class ArgumentsMapperTest extends TestCase
{
    /**
     * @throws \ReflectionException
     */
    public function testGetArguments()
    {
        $argumentsMapper = new ArgumentsMapper(new Application(), new DotNotation(), []);

        $reflectionMethod = new \ReflectionMethod(__CLASS__, 'sampleMethod0');

        $arguments = $argumentsMapper->getArguments($reflectionMethod);

        $this->assertCount(1, $arguments);
        $this->assertContains(['service' => 'silex.application'], $arguments);
    }

    /**
     * @throws \ReflectionException
     */
    public function testGetArgumentsWithMappings()
    {
        $argumentsMapper = new ArgumentsMapper(new Application(), new DotNotation(), []);

        $reflectionMethod = new \ReflectionMethod(__CLASS__, 'sampleMethod0');

        $arguments = $argumentsMapper->getArguments($reflectionMethod, ['app' => 'some.other.service']);

        $this->assertCount(1, $arguments);
        $this->assertContains(['service' => 'some.other.service'], $arguments);
    }

    /**
     * @throws \ReflectionException
     */
    public function testGetArgumentsScalarTypes()
    {
        $argumentsMapper = new ArgumentsMapper(new Application(), new DotNotation(), []);

        $reflectionMethod = new \ReflectionMethod(__CLASS__, 'sampleMethod1');

        $arguments = $argumentsMapper->getArguments($reflectionMethod);

        $this->assertCount(5, $arguments);
        $this->assertContains(['service' => 'silex.application'], $arguments);
        $this->assertContains(['service' => 'int0'], $arguments);
        $this->assertContains(['service' => 'array0'], $arguments);
        $this->assertContains(['value' => '1'], $arguments);
        $this->assertContains(['value' => [1]], $arguments);
    }

    /**
     * @throws \ReflectionException
     */
    public function testGetValuesWithMappings()
    {
        $app = new Application();
        $app['some.service'] = 'service1';
        $app['some.other.service'] = 'service2';

        $argumentsMapper = new ArgumentsMapper($app, new DotNotation(), ['some.service' => 'some.other.service']);

        $reflectionMethod = new \ReflectionMethod(__CLASS__, 'sampleMethod0');

        $arguments = $argumentsMapper->getArguments($reflectionMethod, ['app' => 'some.service']);
        $values = $argumentsMapper->getValues($arguments);

        $this->assertCount(1, $values);
        $this->assertEquals('service2', $values[0]);
    }

    /**
     * @throws \ReflectionException
     */
    public function testGetValuesScalarTypes()
    {
        $argumentsMapper = new ArgumentsMapper(new Application(), new DotNotation(), []);

        $reflectionMethod = new \ReflectionMethod(__CLASS__, 'sampleMethod1');

        $arguments = $argumentsMapper->getArguments($reflectionMethod);
        $values = $argumentsMapper->getValues($arguments);

        $this->assertCount(5, $arguments);
        $this->assertInstanceOf(Application::class, $values[0]);
        $this->assertEquals('int0', $values[1]);
        $this->assertEquals('array0', $values[2]);
        $this->assertEquals('1', $values[3]);
        $this->assertEquals([1], $values[4]);
    }

    public function sampleMethod0(Application $app)
    {
    }

    public function sampleMethod1(Application $app, $int0, array $array0, $int1 = 1, array $array1 = [1])
    {
    }
}
