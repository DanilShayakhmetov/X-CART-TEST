<?php

namespace XLite\Module\Kliken\GoogleAds\Core;

class SchemaFactory extends \XLite\Module\XC\RESTAPI\Core\SchemaFactory implements \XLite\Base\IDecorator
{
    protected static function getSchemaClasses()
    {
        // NOTE: the order of the classes being return MATTERS.
        return array_merge(
            parent::getSchemaClasses(),
            ['\XLite\Module\Kliken\GoogleAds\Core\Schema\Custom']
        );
    }
}
