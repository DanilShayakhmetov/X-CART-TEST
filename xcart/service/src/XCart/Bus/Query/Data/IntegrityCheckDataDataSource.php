<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Query\Data;

use Silex\Application;
use XCart\Bus\Domain\Module;
use XCart\Bus\Domain\Storage\StorageInterface;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service()
 */
class IntegrityCheckDataDataSource extends SerializedDataSource
{
    /**
     * @param Application          $app
     * @param StorageInterface     $storage
     *
     * @return static
     *
     * @Service\Constructor
     * @codeCoverageIgnore
     */
    public static function serviceConstructor(
        Application $app,
        StorageInterface $storage
    ) {
        return new static(
            $storage->build($app['config']['cache_dir'], 'integrityCheckDataStorage')
        );
    }

    /**
     * @param array  $integrityViolations
     * @param Module $module
     *
     * @return bool
     */
    public function appendEntries($integrityViolations, $module): bool
    {
        $data = $this->getAll();

        if (!isset($data[$module->id])) {
            $data[$module->id] = [
                'id'                  => $module->id,
                'name'                => $module->name,
                'author'              => $module->author,
                'integrityViolationsCache' => [
                    'entries'  => [],
                    'isFinal'  => true,
                    'progress' => 0,
                    'error'    => ''
                ]
            ];
        }

        $data[$module->id]['integrityViolationsCache']['entries'] = array_merge(
            $data[$module->id]['integrityViolationsCache']['entries'],
            $integrityViolations['entries']
        );

        return $this->saveAll($data);
    }
}
