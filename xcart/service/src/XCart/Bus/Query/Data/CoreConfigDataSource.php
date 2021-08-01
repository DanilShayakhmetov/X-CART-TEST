<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Query\Data;

use Silex\Application;
use XCart\Bus\Domain\Storage\StorageInterface;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @property string $version
 * @property int    $wave
 * @property string $license
 * @property string $currentEdition
 * @property array  $dbInfo
 * @property bool   $freshInstall
 * @property array  $requestForUpgrade
 * @property int    $dataDate
 * @property int    $cacheDate
 * @property int    $paymentMethodsCacheDate
 * @property int    $shippingMethodsCacheDate
 * @property int    $authLock
 * @property int    $authAttempts
 *
 * @Service\Service()
 */
class CoreConfigDataSource extends SerializedDataSource
{
    /**
     * @param Application      $app
     * @param StorageInterface $storage
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
            $storage->build($app['config']['cache_dir'], 'coreConfigStorage')
        );
    }

    /**
     * @param string $name property name
     *
     * @return mixed
     */
    public function __get($name)
    {
        return $this->find($name);
    }

    /**
     * @param string $name  property name
     * @param mixed  $value property value
     *
     * @return void
     */
    public function __set($name, $value)
    {
        $this->saveOne($value, $name);
    }

    /**
     * @param string $name property name
     *
     * @return bool
     */
    public function __isset($name)
    {
        return (bool) $this->find($name);
    }

    /**
     * @param string $name property name
     */
    public function __unset($name)
    {
        $this->removeOne($name);
    }

    /**
     * @return bool
     */
    protected function shouldAddIdToItemOnSave(): bool
    {
        return false;
    }
}
