<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Reflection;

interface StaticReflectorInterface
{
    // Generic type introspection

    /**
     * @return string
     */
    public function getPathname();

    /**
     * @return string
     */
    public function getNamespace();

    /**
     * @return bool
     */
    public function isAbstract();

    /**
     * @return bool
     */
    public function isClass();

    /**
     * @return bool
     */
    public function isInterface();

    /**
     * @return bool
     */
    public function isTrait();

    /**
     * @return string
     */
    public function getClassName();

    /**
     * @return string
     */
    public function getFQCN();

    /**
     * @return string
     */
    public function getDocCommentText();

    public function getClassAnnotations();

    public function getClassAnnotationsOfType($type);

    public function getParent();

    public function getImplements();

    /**
     * @return bool
     */
    public function isPSR0();

    // Decorator-related functions

    /**
     * @return bool
     */
    public function isDecorator();

    /**
     * Get class module in the form of "Vendor\ModuleName"
     *
     * @return string
     */
    public function getModule();

    /**
     * Get modules which must be present in order for this class to take part in the decoration
     *
     * @return array
     */
    public function getPositiveDependencies();

    /**
     * Get modules which must not be active in order for this class to take part in the decoration
     *
     * @return array
     */
    public function getNegativeDependencies();

    /**
     * Get a list of modules the current class should be placed after
     *
     * @return array
     */
    public function getAfterModules();

    /**
     * Get a list of modules the current class should be placed before
     *
     * @return array
     */
    public function getBeforeModules();

    // Doctrine entities

    /**
     * @return bool
     */
    public function isEntity();

    /**
     * @return bool
     */
    public function isMappedSuperclass();

    /**
     * @return bool
     */
    public function hasLifecycleCallbacks();
}
