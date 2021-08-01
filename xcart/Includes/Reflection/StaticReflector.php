<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Reflection;

use Doctrine\Common\Annotations\AnnotationException;
use Includes\Annotations\Parser\AnnotationParserInterface;
use Includes\ClassPathResolverInterface;
use Includes\Decorator\Utils\Tokenizer;
use Includes\Utils\Module\Manager;
use Includes\Utils\Module\Module;
use XLite\Logger;

class StaticReflector implements StaticReflectorInterface
{
    const DECORATOR_MARKER_INTERFACE = 'XLite\Base\IDecorator';

    /**
     * @var string
     */
    private $pathname;

    /**
     * @var AnnotationParserInterface
     */
    private $annotationParser;

    private $annotationsByType;

    /**
     * @var ClassPathResolverInterface
     */
    private $classPathResolver;

    /**
     * @param ClassPathResolverInterface $classPathResolver
     * @param AnnotationParserInterface  $annotationParser
     * @param string                     $pathname
     */
    public function __construct(
        ClassPathResolverInterface $classPathResolver,
        AnnotationParserInterface $annotationParser,
        $pathname
    ) {
        $this->pathname          = $pathname;
        $this->annotationParser  = $annotationParser;
        $this->classPathResolver = $classPathResolver;
    }

    /**
     * @return string
     */
    public function getPathname()
    {
        return $this->pathname;
    }

    /**
     * @return string
     */
    public function getRealPathname()
    {
        $from = LC_DS === '\\' ? '/' : '\\';

        return str_replace($from, LC_DS, $this->getPathname());
    }

    /**
     * @return string
     */
    public function getNamespace()
    {
        return Tokenizer::getNamespace($this->pathname);
    }

    /**
     * @return bool
     */
    public function isAbstract()
    {
        return Tokenizer::isAbstract($this->pathname);
    }

    /**
     * @return bool
     */
    public function isClass()
    {
        return Tokenizer::getClassName($this->pathname) !== null;
    }

    /**
     * @return bool
     */
    public function isInterface()
    {
        return Tokenizer::getInterfaceName($this->pathname) !== null;
    }

    /**
     * @return bool
     */
    public function isTrait()
    {
        return Tokenizer::getTraitName($this->pathname) !== null;
    }

    /**
     * @return string
     */
    public function getClassName()
    {
        $className = Tokenizer::getClassName($this->pathname);

        if ($className === null) {
            $className = Tokenizer::getInterfaceName($this->pathname);
        }

        if ($className === null) {
            $className = Tokenizer::getTraitName($this->pathname);
        }

        return $className;
    }

    /**
     * @return null|string
     */
    public function getFQCN()
    {
        $className = $this->getClassName();

        if ($className === null) {
            return null;
        }

        $namespace = $this->getNamespace();

        return $namespace
            ? $namespace . '\\' . $className
            : $this->getClassName();
    }

    /**
     * @return string
     */
    public function getDocCommentText()
    {
        return Tokenizer::getDocBlock($this->pathname);
    }

    /**
     * @return array
     */
    public function getClassAnnotations()
    {
        try {
            return $this->annotationParser->parse($this->getDocCommentText());

        } catch (AnnotationException $e) {
            $this->getLogger()->log(sprintf('AnnotationException: %s (%s)', $e->getMessage(), $this->getPathname()), LOG_WARNING);

            return [];
        }
    }

    /**
     * @param string $type
     *
     * @return array
     */
    public function getClassAnnotationsOfType($type)
    {
        if (!isset($this->annotationsByType)) {
            foreach ($this->getClassAnnotations() as $annotation) {
                $annotationClass = get_class($annotation);

                if (!isset($this->annotationsByType[$annotationClass])) {
                    if ($annotationClass === 'Includes\Annotations\LC_Dependencies') {
                        $error = sprintf('@LC_Dependencies annotation is deprecated, use @Decorator\Depend instead (%s)', $this->getPathname());

                        trigger_error($error, E_USER_DEPRECATED);

                        $this->getLogger()->log($error, LOG_WARNING);
                    }

                    $this->annotationsByType[$annotationClass] = [];
                }

                $this->annotationsByType[$annotationClass][] = $annotation;
            }
        }

        return isset($this->annotationsByType[$type]) ? $this->annotationsByType[$type] : [];
    }

    /**
     * @return string
     */
    public function getParent()
    {
        $class = Tokenizer::getParentClassName($this->pathname);

        return ltrim($class, '\\');
    }

    /**
     * @return array
     */
    public function getImplements()
    {
        return array_map(
            function ($interface) {
                return ltrim($interface, '\\');
            },
            Tokenizer::getInterfaces($this->pathname)
        );
    }

    /**
     * @return bool
     */
    public function isPSR0()
    {
        $fqcn = $this->getFQCN();

        return $fqcn !== null && $this->classPathResolver->getPathname($fqcn) === $this->getRealPathname();
    }

    /**
     * @return bool
     */
    public function isDecorator()
    {
        return in_array(self::DECORATOR_MARKER_INTERFACE, $this->getImplements(), true);
    }

    /**
     * @return null|string
     */
    public function getModule()
    {
        return Module::getModuleIdByClassName($this->getNamespace());
    }

    /**
     * @return array
     */
    public function getPositiveDependencies()
    {
        $pos = [];

        /** @var \Includes\Annotations\LC_Dependencies $annotation */
        foreach ($this->getClassAnnotationsOfType('Includes\Annotations\LC_Dependencies') as $annotation) {
            $pos = array_merge($pos, $annotation->dependencies);
        }

        /** @var \Includes\Annotations\Decorator\Depend $annotation */
        foreach ($this->getClassAnnotationsOfType('Includes\Annotations\Decorator\Depend') as $annotation) {
            $pos = array_merge($pos, $annotation->dependencies);
        }

        /** @var \Includes\Annotations\Decorator\Rely $annotation */
        foreach ($this->getClassAnnotationsOfType('Includes\Annotations\Decorator\Rely') as $annotation) {
            $pos = array_merge($pos, $annotation->dependencies);
        }

        return $pos;
    }

    /**
     * @return array
     */
    public function getNegativeDependencies()
    {
        $neg = [];

        /** @var \Includes\Annotations\LC_Dependencies $annotation */
        foreach ($this->getClassAnnotationsOfType('Includes\Annotations\LC_Dependencies') as $annotation) {
            $neg = array_merge($neg, $annotation->incompatibilities);
        }

        /** @var \Includes\Annotations\Decorator\Depend $annotation */
        foreach ($this->getClassAnnotationsOfType('Includes\Annotations\Decorator\Depend') as $annotation) {
            $neg = array_merge($neg, $annotation->incompatibilities);
        }

        /** @var \Includes\Annotations\Decorator\Rely $annotation */
        foreach ($this->getClassAnnotationsOfType('Includes\Annotations\Decorator\Rely') as $annotation) {
            $neg = array_merge($neg, $annotation->incompatibilities);
        }

        return $neg;
    }

    /**
     * @return array
     */
    public function getAfterModules()
    {
        $modules = [];

        /** @var \Includes\Annotations\LC_Dependencies $annotation */
        foreach ($this->getClassAnnotationsOfType('Includes\Annotations\LC_Dependencies') as $annotation) {
            $modules = array_merge($modules, $annotation->dependencies);
        }

        /** @var \Includes\Annotations\Decorator\Depend $annotation */
        foreach ($this->getClassAnnotationsOfType('Includes\Annotations\Decorator\Depend') as $annotation) {
            $modules = array_merge($modules, $annotation->dependencies);
        }

        /** @var \Includes\Annotations\Decorator\After $annotation */
        foreach ($this->getClassAnnotationsOfType('Includes\Annotations\Decorator\After') as $annotation) {
            $modules = array_merge($modules, $annotation->modules);
        }

        return $modules;
    }

    /**
     * @return array
     */
    public function getBeforeModules()
    {
        $modules = [];

        /** @var \Includes\Annotations\Decorator\Before $annotation */
        foreach ($this->getClassAnnotationsOfType('Includes\Annotations\Decorator\Before') as $annotation) {
            $modules = array_merge($modules, $annotation->modules);
        }

        return $modules;
    }

    /**
     * @return bool
     */
    public function isEntity()
    {
        return $this->isModel() && $this->getClassAnnotationsOfType('Doctrine\ORM\Mapping\Entity');
    }

    /**
     * @return bool
     */
    public function isMappedSuperclass()
    {
        return $this->isModel() && $this->getClassAnnotationsOfType('Doctrine\ORM\Mapping\MappedSuperclass');
    }

    /**
     * @return bool
     */
    public function hasLifecycleCallbacks()
    {
        return $this->isModel() && $this->getClassAnnotationsOfType('Doctrine\ORM\Mapping\HasLifecycleCallbacks');
    }

    /**
     * @return bool
     */
    private function isModel()
    {
        $parts = explode('\\', $this->getNamespace());

        return (count($parts) > 1 && $parts[1] === 'Model')
            || (count($parts) > 4 && $parts[1] === 'Module' && $parts[4] === 'Model');
    }

    /**
     * @return Logger
     */
    protected function getLogger()
    {
        return Logger::getInstance();
    }
}
