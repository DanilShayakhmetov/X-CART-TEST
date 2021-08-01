<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Reflection;

use Includes\Decorator\Utils\Tokenizer;

class ClassTransformer implements ClassTransformerInterface
{
    private $sourcePathname;

    private $isAbstract;

    private $className;

    private $docComment;

    private $extends;

    public function __construct($sourcePathname)
    {
        $this->sourcePathname = $sourcePathname;
    }

    /**
     * @param $class
     *
     * @return ClassTransformerInterface
     */
    public function setClassName($class)
    {
        $this->className = $class;

        return $this;
    }

    /**
     * @param $isAbstract
     *
     * @return ClassTransformerInterface
     */
    public function setAbstract($isAbstract)
    {
        $this->isAbstract = $isAbstract;

        return $this;
    }

    /**
     * @param $class
     *
     * @return ClassTransformerInterface
     */
    public function setParent($class)
    {
        $this->extends = $class;

        return $this;
    }

    /**
     * @param $text
     *
     * @return ClassTransformerInterface
     */
    public function setDocComment($text)
    {
        $this->docComment = $text;

        return $this;
    }

    public function removeAnnotations(array $annotations)
    {
        $text = $this->getDocCommentText();

        foreach ($annotations as $annotation) {
            $text = preg_replace('/@(' . $annotation . '\b)/i', ' $1', $text);
        }

        return $this->setDocComment($text);
    }

    public function removeEntityAnnotations()
    {
        $removeAnnotations = [
            'Entity',
            'Table',
            'Index',
            'UniqueConstraint',
            'InheritanceType',
            'DiscriminatorColumn',
            'DiscriminatorMap',
        ];

        return $this->removeAnnotations($removeAnnotations);
    }

    public function removeApiDocAnnotations()
    {
        $removeAnnotations = [
            'Api\\\Entity',
            'Api\\\Column',
            'Api\\\Association',
            'Api\\\Condition',
            'Api\\\Operation\\\Create',
            'Api\\\Operation\\\Read',
            'Api\\\Operation\\\ReadAll',
            'Api\\\Operation\\\Update',
            'Api\\\Operation\\\Delete',
            'Swg\\\AbstractAnnotation',
            'Swg\\\Contact',
            'Swg\\\Definition',
            'Swg\\\Delete',
            'Swg\\\ExternalDocumentation',
            'Swg\\\Get',
            'Swg\\\Head',
            'Swg\\\Header',
            'Swg\\\Info',
            'Swg\\\Items',
            'Swg\\\License',
            'Swg\\\Operation',
            'Swg\\\Options',
            'Swg\\\Parameter',
            'Swg\\\Patch',
            'Swg\\\Path',
            'Swg\\\Post',
            'Swg\\\Property',
            'Swg\\\Put',
            'Swg\\\Response',
            'Swg\\\Schema',
            'Swg\\\SecurityScheme',
            'Swg\\\Swagger',
            'Swg\\\Tag',
            'Swg\\\Xml',
        ];

        return $this->removeAnnotations($removeAnnotations);
    }

    /**
     * @return string
     */
    public function getSource()
    {
        return Tokenizer::getSourceCode(
            $this->sourcePathname,
            null,
            $this->getClassName(),
            $this->getExtends(),
            $this->getDocCommentText(),
            $this->isAbstract() ? 'abstract' : ''
        );
    }

    private function getClassName()
    {
        if ($this->className == null) {
            $this->className = Tokenizer::getClassName($this->sourcePathname);
        }

        return $this->className;
    }

    private function getDocCommentText()
    {
        if ($this->docComment == null) {
            $this->docComment = Tokenizer::getDocBlock($this->sourcePathname);
        }

        return $this->docComment;
    }

    private function getExtends()
    {
        if ($this->extends == null) {
            $this->extends = Tokenizer::getParentClassName($this->sourcePathname);
        }

        return $this->extends;
    }

    private function isAbstract()
    {
        if ($this->isAbstract == null) {
            $this->isAbstract = Tokenizer::isAbstract($this->sourcePathname);
        }

        return $this->isAbstract;
    }
}