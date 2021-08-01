<?php

namespace XCart\SilexAnnotationsTest\ServiceAnnotation;

use PHPUnit\Framework\TestCase;
use XCart\SilexAnnotations\NameConverter\DotNotation;

class DotNotationTest extends TestCase
{
    /**
     * @param string $expected
     * @param string $className
     *
     * @dataProvider providerClassNameToServiceName
     */
    public function testClassNameToServiceName($expected, $className)
    {
        $nameConverter = new DotNotation();

        $serviceName = $nameConverter->classNameToServiceName($className);

        $this->assertEquals($expected, $serviceName);
    }

    public function providerClassNameToServiceName()
    {
        return [
            ['silex.application', 'Silex\\Application'],
            ['silex.application', '\\Silex\\Application'],
            ['some.namespace.some_class_name', 'Some\\Namespace\\SomeCLASSName'],
        ];
    }
}
