<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MultiCurrency\Core;


/**
 * CountriesParser
 */
class CountriesParser
{
    private $data = [];

    /**
     * CountriesParser constructor.
     *
     * @param $path
     */
    public function __construct($path = null)
    {
        if (is_null($path)) {
            $path = implode(LC_DS, [
                LC_DIR_MODULES,
                'XC',
                'MultiCurrency',
                'resources',
                'countries.yaml',
            ]);
        }

        if (file_exists($path)) {
            $fileContent = file_get_contents($path);
            if ($fileContent) {
                $this->data = \Symfony\Component\Yaml\Yaml::parse($fileContent);
            }
        } else {
            \XLite\Logger::getInstance()->log("File {$path} not found", LOG_WARNING);
        }
    }

    /**
     * @param string $currencyCode ISO_4217
     *
     * @return array
     */
    public function getCurrencyCountries($currencyCode)
    {
        if (isset($this->data[strtoupper($currencyCode)])) {
            /** @var $qb \XLite\Model\QueryBuilder\AQueryBuilder */
            $qb = \XLite\Core\Database::getRepo('XLite\Model\Country')->createPureQueryBuilder();
            $alias = $qb->getMainAlias();

            $qb->andWhere("{$alias}.code IN (:codes)")
                ->setParameter('codes', $this->data[strtoupper($currencyCode)]);

            return $qb->getResult();
        }

        return [];
    }

    /**
     * @param string $currencyCode ISO_4217
     *
     * @return array
     */
    public function getCurrencyCountryCodes($currencyCode)
    {
        if (isset($this->data[strtoupper($currencyCode)])) {
            return $this->data[strtoupper($currencyCode)];
        }

        return [];
    }
}