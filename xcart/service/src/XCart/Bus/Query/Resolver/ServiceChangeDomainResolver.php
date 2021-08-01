<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Query\Resolver;

use Silex\Application;
use XCart\Bus\Query\Context;
use GraphQL\Type\Definition\ResolveInfo;
use XCart\Bus\Core\Annotations\Resolver;
use XCart\ConfigParser\ConfigParser;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service()
 */
class ServiceChangeDomainResolver
{

    protected $config;

    /**
     * @Service\Constructor
     * @codeCoverageIgnore
     **/
    public static function serviceConstructor(Application $app)
    {
        return new self($app['config']['root_dir']);
    }

    public function __construct($rootDir)
    {
        $this->config = new ConfigParser($_SERVER, $rootDir . '/etc/');
    }

    /**
     * @param             $value
     * @param             $args
     * @param Context     $context
     * @param ResolveInfo $info
     *
     * @return array|bool[]
     * @throws \Exception
     * @throws \XCart\ConfigParser\ConfigMissingFileException
     * @throws \XCart\ConfigParser\ConfigWrongFormattedFileException
     * @Resolver()
     */
    public function setNewDomainName($value, $args, Context $context, ResolveInfo $info): array
    {
        $newDomain = $args['domain'] ?? null;

        if (empty($newDomain)) {
            throw new \Exception('Domain is required');
        }

        $currentDomain = $this->config->getOption('host_details', 'http_host');
        $adminDomain   = $this->config->getOption('host_details', 'admin_host');
        $success       = true;

        if (empty($adminDomain)) {
            $success = $this->config->setOption('host_details', 'admin_host', '"' . $currentDomain . '"') && $success;
        }

        if ($newDomain !== $currentDomain) {
            $success = $this->config->setOption('host_details', 'http_host', '"' . $newDomain . '"') && $success;
            $success = $this->config->setOption('host_details', 'https_host', '"' . $newDomain . '"') && $success;
        }

        return [
            'success' => $success,
            'changed' => $success && $newDomain !== $currentDomain,
        ];
    }
}