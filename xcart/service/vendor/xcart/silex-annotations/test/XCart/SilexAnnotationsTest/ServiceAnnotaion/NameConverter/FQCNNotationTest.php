<?php

namespace XCart\SilexAnnotationsTest\ServiceAnnotation;

use PHPUnit\Framework\TestCase;
use XCart\SilexAnnotations\NameConverter\FQCNNotation;

class FQCNNotationTest extends TestCase
{
    /**
     * @param string $expected
     * @param string $className
     *
     * @dataProvider providerClassNameToServiceName
     */
    public function testClassNameToServiceName($expected, $className)
    {
        $nameConverter = new FQCNNotation();

        $serviceName = $nameConverter->classNameToServiceName($className);

        $this->assertEquals($expected, $serviceName);
    }

    public function providerClassNameToServiceName()
    {
        return [
            ['Silex\\Application', 'Silex\\Application'],
            ['Silex\\Application', '\\Silex\\Application'],
            ['Some\\Namespace\\SomeCLASSName', 'Some\\Namespace\\SomeCLASSName'],
        ];
    }
}
