<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Console\Command\SourceCodeGenerators;


class Module
{
    public static function generate($devId, $name, $readableName, $description = '')
    {
        $version = \XLite::getInstance()->getMajorVersion();
        return <<<PHP
<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\\$devId\\$name;

/**
 * Module main class
 */
abstract class Main extends \XLite\Module\AModule
{
    public static function getAuthorName()
    {
        return '$devId';
    }
    
    public static function getMajorVersion()
    {
        return '$version';
    }
    
    public static function getMinorVersion()
    {
        return '0';
    }
    
    public static function getBuildVersion()
    {
        return '0';
    }
    
    public static function getMinorRequiredCoreVersion()
    {
        return '0';
    }
    
    public static function getModuleName()
    {
        return '$readableName';
    }
    
    public static function getDescription()
    {
        return '$description';
    }
}

PHP;

    }
    public static function generatePostFill($devId, $name)
    {
        return <<<PHP
<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\\$devId\\$name;

/**
 * Module main class
 */
abstract class Main extends \XLite\Module\AModule
{
}

PHP;

    }
}