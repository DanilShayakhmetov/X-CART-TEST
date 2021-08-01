<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Query\Resolver;

use GraphQL\Type\Definition\ResolveInfo;
use XCart\Bus\Core\Annotations\Resolver;
use XCart\Bus\Domain\Module;
use XCart\Bus\Domain\ServiceDataProvider;
use XCart\Bus\Query\Data\InstalledModulesDataSource;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service()
 */
class LanguageDataResolver
{
    /**
     * @var InstalledModulesDataSource
     */
    private $installedModulesDataSource;

    /**
     * @var ServiceDataProvider
     */
    private $serviceDataProvider;

    /**
     * @param InstalledModulesDataSource $installedModulesDataSource
     * @param ServiceDataProvider        $serviceDataProvider
     */
    public function __construct(
        InstalledModulesDataSource $installedModulesDataSource,
        ServiceDataProvider $serviceDataProvider
    ) {
        $this->installedModulesDataSource = $installedModulesDataSource;
        $this->serviceDataProvider        = $serviceDataProvider;
    }

    /**
     * @param string $message
     * @param array  $params
     *
     * @return string
     */
    public static function getMessageWithReplacedParams($message, $params)
    {
        $keys   = [];
        $values = [];
        foreach ($params as $k => $v) {
            $keys[]   = '{' . $k . '}';
            $values[] = $v;
        }

        return str_replace($keys, $values, $message);
    }

    /**
     * @param             $value
     * @param             $args
     * @param             $context
     * @param ResolveInfo $info
     *
     * @return array
     *
     * @Resolver()
     */
    public function getLanguages($value, $args, $context, ResolveInfo $info): array
    {
        return $this->installedModulesDataSource->getLanguages();
    }

    /**
     * @param             $value
     * @param             $args
     * @param             $context
     * @param ResolveInfo $info
     *
     * @return array
     *
     * @Resolver()
     */
    public function getLanguageMessages($value, $args, $context, ResolveInfo $info)
    {
        $service = $this->serviceDataProvider->getCoreServiceData();
        $result  = $service['LanguageLabel'];

        if (isset($args['code']) && $args['code'] !== 'en') {
            $result = $this->convertToArray(
                $this->convertToHash($this->getLanguageMessagesByCode($args['code'])) + $this->convertToHash($result)
            );
        }

        return $result;
    }

    /**
     * @param string $code
     *
     * @return array
     */
    private function getLanguageMessagesByCode($code): array
    {
        /** @var Module $module */
        foreach ($this->installedModulesDataSource->getAll() as $module) {
            if (!$module->enabled || !isset($module->service['Language'])) {
                continue;
            }

            if ($module->service['Language']['code'] === $code) {
                return $module->service['LanguageLabel'];
            }
        }

        return [];
    }

    /**
     * @param array $languageMessages
     *
     * @return array
     */
    private function convertToHash($languageMessages)
    {
        $result = [];

        foreach ($languageMessages as $languageMessage) {
            $result[$languageMessage['name']] = $languageMessage['label'];
        }

        return $result;
    }

    /**
     * @param array $languageMessages
     *
     * @return array
     */
    private function convertToArray($languageMessages)
    {
        $result = [];

        foreach ($languageMessages as $name => $label) {
            $result[] = [
                'name' => $name,
                'label' => $label
            ];
        }

        return $result;
    }
}
