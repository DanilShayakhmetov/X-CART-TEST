<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\FreeShipping\Core;


use Includes\Utils\FileManager;
use XLite\Core\Database;

class MethodsLoader
{
    public static function process()
    {
        try {
            if (static::loadMethods(static::getMethodsData())) {
                Database::getEM()->flush();
            }
        } catch (\Exception $e) {
            \XLite\Logger::getInstance()->log($e->getMessage(), LOG_ERR);
        }
    }

    /**
     * @return mixed
     */
    protected static function getMethodsData()
    {
        $parser = new \Symfony\Component\Yaml\Parser;

        $data = $parser->parse(FileManager::read(LC_DIR_MODULES . 'XC/FreeShipping/data/methods.yaml'));

        return $data['XLite\Model\Shipping\Method'];
    }

    /**
     * @param $data
     *
     * @return array
     */
    protected static function loadMethods($data)
    {
        return array_map(function ($e) {
            static::loadMethod($e);
            return $e;
        }, array_filter($data, function ($e) {
            return !static::isMethodLoaded($e['code']);
        }));
    }

    /**
     * @param $code
     *
     * @return bool
     */
    protected static function isMethodLoaded($code)
    {
        $repo = \XLite\Core\Database::getRepo('XLite\Model\Shipping\Method');

        return (boolean)$repo->findOneBy([
            'free' => $code === 'FREESHIP',
            'code' => $code
        ]);
    }

    /**
     * @param $data
     *
     * @return \XLite\Model\AEntity
     */
    protected static function loadMethod($data)
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Shipping\Method')->loadFixture($data);
    }
}