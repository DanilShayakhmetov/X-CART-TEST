<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CustomProductTabs\Model\Repo\Product;

use Includes\Utils\Module\Manager;
use XLite\Core\Database;
use XLite\Model\Product\GlobalTabProvider;

/**
 * Global tabs repository
 */
class GlobalTab extends \XLite\Model\Repo\Product\GlobalTab implements \XLite\Base\IDecorator
{
    const SEARCH_BY_ENABLED_MODULES = 'byEnabledModules';

    /**
     * Flush unit-of-work changes after every record loading
     *
     * @var boolean
     */
    protected $flushAfterLoading = true;

    /**
     * Returns minimal position
     *
     * @return integer
     */
    public function getMinPosition()
    {
        $qb = $this->createQueryBuilder('gt');
        return $qb->select('MIN(gt.position)')->getSingleScalarResult();
    }

    /**
     * Returns maximal position
     *
     * @return integer
     */
    public function getMaxPosition()
    {
        $qb = $this->createQueryBuilder('gt');
        return $qb->select('MAX(gt.position)')->getSingleScalarResult();
    }

    /**
     * Create all nonexistent global tab aliases
     */
    public function createNonExistentAliases()
    {
        $shiftValue = $this->getMaxPosition() + 10;
        $tablePrefix = \XLite::getInstance()->getOptions(['database_details', 'table_prefix']);
        $tabsTable = $tablePrefix . 'product_tabs';
        $productsTable = $tablePrefix . 'products';
        $globalTabsTable = $tablePrefix . 'global_product_tabs';

        $query = "INSERT IGNORE " .
            "INTO {$tabsTable}(enabled, product_id, global_tab_id, position) " .
            "SELECT " .
            "gt.enabled as enabled, " .
            "p.product_id as product_id, " .
            "gt.id as global_tab_id, " .
            "(gt.position - :shiftValue) as position " .
            "FROM {$globalTabsTable} as gt, {$productsTable} as p";

        Database::getEM()->getConnection()->executeQuery($query, [
            'shiftValue' => $shiftValue,
        ]);
    }

    /**
     * Create nonexistent global tab aliases
     *
     * @param \XLite\Model\Product\GlobalTab $globalTab
     */
    public function createGlobalTabAliases($globalTab)
    {
        $shiftValue = $this->getMaxPosition() + 10;

        $tablePrefix = \XLite::getInstance()->getOptions(['database_details', 'table_prefix']);
        $tabsTable = $tablePrefix . 'product_tabs';
        $productsTable = $tablePrefix . 'products';


        $enabled = (integer)$globalTab->getEnabled();
        $globalTabId = $globalTab->getId();
        $position = $globalTab->getPosition() - $shiftValue;

        $query = "INSERT IGNORE "
            . "INTO {$tabsTable}(enabled, product_id, global_tab_id, position) "
            . "SELECT :enabled as enabled, p.product_id as product_id, :globalTabId as global_tab_id, :position as position "
            . "FROM {$productsTable} as p";

        Database::getEM()->getConnection()->executeQuery($query, [
            'enabled'     => $enabled,
            'globalTabId' => $globalTabId,
            'position'    => $position,
        ]);
    }

    /**
     * Create nonexistent global tab aliases for product
     *
     * @param \XLite\Model\Product $product
     */
    public function createGlobalTabsAliases($product)
    {
        $shiftValue = $this->getMaxPosition() + 10;

        $tablePrefix = \XLite::getInstance()->getOptions(['database_details', 'table_prefix']);
        $tabsTable = $tablePrefix . 'product_tabs';
        $globalTabsTable = $tablePrefix . 'global_product_tabs';

        $query = "INSERT IGNORE "
            . "INTO {$tabsTable}(enabled, product_id, global_tab_id, position) "
            . "SELECT gt.enabled as enabled, :productId as product_id, gt.id as global_tab_id, (gt.position - :shiftValue) as position "
            . "FROM {$globalTabsTable} as gt";

        Database::getEM()->getConnection()->executeQuery($query, [
            'productId'  => $product->getId(),
            'shiftValue' => $shiftValue,
        ]);
    }

    /**
     * Update global tabs aliases with global tabs values
     */
    public function updateAliases()
    {
        $shiftValue = $this->getMaxPosition() + 10;

        $tablePrefix = \XLite::getInstance()->getOptions(['database_details', 'table_prefix']);
        $tabsTable = $tablePrefix . 'product_tabs';
        $globalTabsTable = $tablePrefix . 'global_product_tabs';

        $query = "UPDATE {$tabsTable} as t "
            . "INNER JOIN {$globalTabsTable} as gt "
            . "ON gt.id = t.global_tab_id "
            . "SET "
            . "t.enabled = gt.enabled, t.position = gt.position - :shiftValue"
            . " WHERE t.global_tab_id IS NOT NULL";

        Database::getEM()->getConnection()->executeQuery($query, [
            'shiftValue' => $shiftValue,
        ]);
    }

    /**
     * Load raw fixture
     *
     * @param \XLite\Model\AEntity $entity  Entity
     * @param array                $record  Record
     * @param array                $regular Regular fields info OPTIONAL
     * @param array                $assocs  Associations info OPTIONAL
     *
     * @return void
     */
    public function loadRawFixture(
        \XLite\Model\AEntity $entity,
        array $record,
        array $regular = [],
        array $assocs = []
    )
    {
        $persistent = $entity->isPersistent();

        parent::loadRawFixture($entity, $record, $regular, $assocs);

        if (!$persistent) {
            $this->createGlobalTabAliases($entity);
        }
    }

    /**
     * @param \XLite\Model\Product\GlobalTab|\XLite\Module\XC\CustomProductTabs\Model\Product\CustomGlobalTab $tab
     *
     * @return string
     */
    public function generateTabLink(\XLite\Module\XC\CustomProductTabs\Model\Product\CustomGlobalTab $tab)
    {
        $result = $link = preg_replace(
            '/[^a-z0-9-_]/i',
            '',
            str_replace(
                ' ',
                '_',
                \XLite\Core\Converter::convertToTranslit($tab->getName())
            )
        );

        $i = 1;
        while (!$this->checkLinkUniqueness($result, $tab)) {
            $result = $link . '_' . $i;
            $i++;
        }

        return $result;
    }

    /**
     * @param string                                                           $link
     * @param \XLite\Module\XC\CustomProductTabs\Model\Product\CustomGlobalTab $tab
     *
     * @return bool
     *
     */
    public function checkLinkUniqueness($link, \XLite\Module\XC\CustomProductTabs\Model\Product\CustomGlobalTab $tab)
    {
        $result = !$this->findOneBy([
                'link' => $link,
            ]) && !$this->findOneBy([
                'service_name' => $link,
            ]) && !\XLite\Core\Database::getRepo('XLite\Module\XC\CustomProductTabs\Model\Product\Tab')->findOneBy([
                'link' => $link,
            ]);

        return $result;
    }

    /**
     * Prepare certain search condition
     *
     * @param \XLite\Model\QueryBuilder\AQueryBuilder $queryBuilder Query builder to prepare
     * @param boolean                                 $value        Condition data
     * @param boolean                                 $countOnly    "Count only" flag. Do not need to add "order by" clauses if only count is needed.
     *
     * @return void
     */
    protected function prepareCndByEnabledModules(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        if ($value) {
            /** @var \XLite\Model\Product\GlobalTabProvider[] $providers */
            $providers = Database::getRepo('\XLite\Model\Product\GlobalTabProvider')->findAll();
            $tabsModules = [
                GlobalTabProvider::PROVIDER_CORE => GlobalTabProvider::PROVIDER_CORE,
            ];
            foreach ($providers as $provider) {
                $code = $provider->getCode();
                if (GlobalTabProvider::PROVIDER_CORE === $code) {
                    continue;
                }

                $module = Manager::getRegistry()->getModule($code);
                if ($module && $module->isEnabled()) {
                    $tabsModules[$code] = $code;
                }
            }

            $alias = $queryBuilder->getMainAlias();
            $queryBuilder->linkLeft("{$alias}.providers", 'providers');

            $or = new \Doctrine\ORM\Query\Expr\Orx();

            $or->add("{$alias}.service_name IS NULL")
               ->add('providers.code IN (:codes)');

            $queryBuilder
                ->andWhere($or)
                ->setParameter('codes', array_values($tabsModules));
        }
    }
}