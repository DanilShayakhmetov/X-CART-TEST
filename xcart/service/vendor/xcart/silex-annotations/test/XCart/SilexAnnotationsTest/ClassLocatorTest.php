<?php

namespace XCart\SilexAnnotationsTest;

use PHPUnit\Framework\TestCase;
use XCart\SilexAnnotations\ClassLocator;
use XCart\SilexAnnotationsTest\Fixtures\ClassLocatorSample;
use XCart\SilexAnnotationsTest\Fixtures\ClassLocatorSample2;

class ClassLocatorTest extends TestCase
{
    public function testGetClasses()
    {
        $classLocator = new ClassLocator(__DIR__ . '/../../');

        $classes = $classLocator->getClasses(__DIR__ . '/Fixtures/ClassLocatorSample');

        $this->assertCount(2, $classes);
        $this->assertContains(ClassLocatorSample\SimpleClass::class, $classes);
        $this->assertContains(ClassLocatorSample\Simple\SimpleClass::class, $classes);
    }

    public function testGetClassesAbsentClass()
    {
        $classLocator = new ClassLocator(__DIR__ . '/../../');

        $classes = $classLocator->getClasses(__DIR__ . '/Fixtures/ClassLocatorSample2');

        $this->assertCount(1, $classes);
        $this->assertContains(ClassLocatorSample2\SimpleClass::class, $classes);

        $this->assertNotContains('XCart\SilexAnnotationsTest\ClassLocatorSample2\NoClass', $classes);
    }

    public function testGetClassesSeveralLocations()
    {
        $classLocator = new ClassLocator(__DIR__ . '/../../');

        $classes = $classLocator->getClasses([
            __DIR__ . '/Fixtures/ClassLocatorSample',
            __DIR__ . '/Fixtures/ClassLocatorSample2'
        ]);

        $this->assertCount(3, $classes);
        $this->assertContains(ClassLocatorSample\SimpleClass::class, $classes);
        $this->assertContains(ClassLocatorSample\Simple\SimpleClass::class, $classes);
        $this->assertContains(ClassLocatorSample2\SimpleClass::class, $classes);
    }
}
