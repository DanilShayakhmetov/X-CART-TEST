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
 * @Service\Service()
 */
class LicenseDataSource extends SerializedDataSource
{
    public const KEY_TYPE_CORE = 2;
    public const KEY_TYPE_PENDING = 'pending';

    /**
     * @var CoreConfigDataSource
     */
    private $coreConfigDataSource;

    /**
     * @param Application          $app
     * @param StorageInterface     $storage
     * @param CoreConfigDataSource $coreConfigDataSource
     *
     * @return static
     *
     * @Service\Constructor
     * @codeCoverageIgnore
     */
    public static function serviceConstructor(
        Application $app,
        StorageInterface $storage,
        CoreConfigDataSource $coreConfigDataSource
    ) {
        return new static(
            $storage->build($app['config']['cache_dir'], 'licenseStorage'),
            $coreConfigDataSource
        );
    }

    /**
     * @param StorageInterface     $storage
     * @param CoreConfigDataSource $coreCoreConfigDataSource
     */
    public function __construct(
        StorageInterface $storage,
        CoreConfigDataSource $coreCoreConfigDataSource
    ) {
        parent::__construct($storage);

        $this->coreConfigDataSource = $coreCoreConfigDataSource;
    }

    /**
     * @return array
     */
    public function getAll(): array
    {
        $result = parent::getAll();

        // TODO: remove after some time (see BUG-8625)
        $wrongItems = array_filter($result, function ($item) {
            return is_null($item['author']) || is_null($item['name']);
        });

        if (!empty($wrongItems)) {
            $result = array_diff_key($result, $wrongItems);
            $this->saveAll($result);
        }

        return $result;
    }

    /**
     * @param array $condition
     *
     * @return array
     */
    public function findBy($condition): array
    {
        foreach ($this->getAll() as $keyInfo) {
            if (!array_diff($condition, array_intersect_key($keyInfo, $condition))) {
                return $keyInfo;
            }
        }

        return [];
    }

    /**
     * @param array $keyInfo
     *
     * @return bool
     */
    public function isCoreKey($keyInfo): bool
    {
        return isset($keyInfo['keyType']) && (int) $keyInfo['keyType'] === self::KEY_TYPE_CORE;
    }

    /**
     * @param array $keyInfo
     *
     * @return bool
     */
    public function isFreeCoreKey($keyInfo): bool
    {
        if ((int) $keyInfo['xcnPlan'] === 2) {
            return true;
        }

        return isset($keyInfo['keyData']['editionName']) && 'Free' === $keyInfo['keyData']['editionName'];
    }

    /**
     * @param array $data
     *
     * @return bool
     */
    public function saveAll(array $data): bool
    {
        $this->coreConfigDataSource->saveOne(time(), 'dataDate');

        return parent::saveAll($data);
    }

    /**
     * @param array $data
     *
     * @return bool
     */
    public function updateAll(array $data): bool
    {
        foreach ($this->getAll() as $keyId => $keyInfo) {
            $keyValue = $keyInfo['keyValue'];
            if (is_array($data[$keyValue] ?? null)) {
                foreach ($data[$keyValue] as $info) {
                    if ('CDev' === $info['author'] && 'Core' === $info['name']) {
                        $isValid = true;

                    } else {
                        $isValid = true; // todo: check for module exists in storage
                    }

                    if ($isValid) {
                        $info['active']   = true;
                        $info['keyValue'] = $keyValue;
                        $this->saveOne($info, $keyId);
                    } else {
                        $this->removeOne($keyId);
                    }
                }
            } else {
                $keyInfo['active'] = false;
                $this->saveOne($keyInfo, $keyId);
            }
        }

        return true;
    }

    /**
     * @param array $item
     *
     * @return string
     */
    protected function buildItemId($item): string
    {
        return implode('-', [$item['author'], $item['name'], $item['keyType']]);
    }

    /**
     * @param string $author
     * @param string $name
     *
     * @return bool
     */
    public function savePending($author, $name): bool
    {
        return $this->saveOne([
            'author' => $author,
            'name' => $name,
            'active' => true,
            'keyType' => static::KEY_TYPE_PENDING,
            'keyValue' => static::KEY_TYPE_PENDING
        ]);
    }

    /**
     * @param array $item
     *
     * @return bool
     */
    public function removePending($item): bool
    {
        $item['keyType'] = static::KEY_TYPE_PENDING;
        $pendingId = $this->buildItemId($item);

        return $this->removeOne($pendingId);
    }
}
