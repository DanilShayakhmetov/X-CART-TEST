<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\SilexAnnotations;

class SilexAnnotationsException extends \RuntimeException
{
    /**
     * @param string $optionName
     *
     * @return self
     */
    public static function fromMissingRequiredConfiguration($optionName)
    {
        return new self(
            sprintf(
                'Configuration error. \'%s\' option in required.',
                $optionName
            )
        );
    }

    /**
     * @param string $serviceName
     *
     * @return self
     */
    public static function fromFrozenService($serviceName)
    {
        return new self(
            sprintf(
                'Can\'t redeclare frozen service \'%s\'.',
                $serviceName
            )
        );
    }

    /**
     * @param string $message
     *
     * @return self
     */
    public static function fromReaderInstantiation($message)
    {
        return new self(
            sprintf(
                'Can\'t instantiate annotation reader: %s',
                $message
            )
        );
    }

    /**
     * @param string $className
     *
     * @return self
     */
    public static function fromServiceInstantiation($className)
    {
        return new self(
            sprintf(
                'Can\'t instantiate annotation service: %s',
                $className
            )
        );
    }
}