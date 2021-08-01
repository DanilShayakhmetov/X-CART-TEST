<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CustomProductTabs\Logic\Import\Processor;

use XLite\Core\Database;
use XLite\Module\XC\CustomProductTabs\Model\Product\Tab;

/**
 * Product tabs import processor
 */
class CustomTabs extends \XLite\Logic\Import\Processor\AProcessor
{
    /**
     * Check - specified file is imported by this processor or not
     *
     * @param \SplFileInfo $file File
     *
     * @return boolean
     */
    protected function isImportedFile(\SplFileInfo $file)
    {
        return 0 === strpos($file->getFilename(), 'product-custom-tabs');
    }

    /**
     * Get import file name format
     *
     * @return string
     */
    public function getFileNameFormat()
    {
        return 'product-custom-tabs.csv';
    }

    /**
     * Get title
     *
     * @return string
     */
    public static function getTitle()
    {
        return static::t('Tabs imported');
    }

    /**
     * Get repository
     *
     * @return \XLite\Model\Repo\ARepo
     */
    protected function getRepository()
    {
        return Database::getRepo('XLite\Module\XC\CustomProductTabs\Model\Product\Tab');
    }

    /**
     * Define columns
     *
     * @return array
     */
    protected function defineColumns()
    {
        return [
            'product'      => [
                static::COLUMN_IS_KEY      => true,
                static::COLUMN_IS_REQUIRED => true,
            ],
            'name'         => [
                static::COLUMN_IS_MULTILINGUAL => true,
                static::COLUMN_IS_REQUIRED     => true,
            ],
            'content'      => [
                static::COLUMN_IS_MULTILINGUAL => true,
                static::COLUMN_IS_TAGS_ALLOWED => true,
            ],
            'brief_info'   => [
                static::COLUMN_IS_MULTILINGUAL => true,
                static::COLUMN_IS_TAGS_ALLOWED => true,
            ],
            'enabled'      => [],
            'position'     => [],
            'alias'        => [],
            'service_name' => [],
            'link'         => [
                static::COLUMN_IS_KEY          => true,
            ],
            'global_tab'   => [
                static::COLUMN_IS_MULTICOLUMN  => true,
                static::COLUMN_HEADER_DETECTOR => true,
                static::COLUMN_IS_IMPORT_EMPTY => true
            ]
        ];
    }

    /**
     * Detect details header(s)
     *
     * @param array $column Column info
     * @param array $row    Header row
     *
     * @return array
     */
    protected function detectGlobalTabHeader(array $column, array $row)
    {
        return $this->detectHeaderByPattern('(alias|link|service_name)', $row);
    }

    /**
     * Detect model
     *
     * @param array $data Data
     *
     * @return \XLite\Model\AEntity
     */
    protected function detectModel(array $data)
    {
        if (isset($data['alias']) && $this->normalizeValueAsBoolean($data['alias'])) {
            $qb = $this->getRepository()->createQueryBuilder('t');

            $qb->linkInner('t.product', 'p')
                ->andWhere('p.sku = :sku AND t.global_tab = :globalTab')
                ->setParameter(
                    'globalTab',
                    empty($data['service_name'])
                        ? $this->getGlobalTabByLink($data['link'])
                        : $this->getGlobalTabByServiceName($data['service_name'])
                )
                ->setParameter('sku', $data['product']);

            return $qb->getSingleResult();
        } else {
            if (!$data['link']) {
                return null;
            }

            $qb = $this->getRepository()->createQueryBuilder('t');

            $qb->linkInner('t.product', 'p')
                ->andWhere('p.sku = :sku AND t.link = :link AND t.global_tab IS NULL')
                ->setParameter('sku', $data['product'])
                ->setParameter('link', $data['link']);

            return $qb->getSingleResult();
        }
    }

    /**
     * Find global tab by service name
     *
     * @param string $serviceName
     *
     * @return null|\XLite\Model\Product\GlobalTab
     */
    protected function getGlobalTabByServiceName($serviceName)
    {
        $qb = Database::getRepo('XLite\Model\Product\GlobalTab')->createQueryBuilder('gt');

        $qb->andWhere('gt.service_name = :service_name')
            ->setParameter('service_name', $serviceName);

        return $qb->getSingleResult();
    }

    /**
     * Find global tab by link
     *
     * @param array $link
     *
     * @return null|\XLite\Model\Product\GlobalTab
     */
    protected function getGlobalTabByLink($link)
    {
        $qb = Database::getRepo('XLite\Model\Product\GlobalTab')->createQueryBuilder('gt');

        $qb->andWhere('gt.link = :link')
            ->setParameter('link', $link);

        return $qb->getSingleResult();
    }

    /**
     * Create model
     *
     * @param array $data Data
     *
     * @return \XLite\Model\AEntity
     */
    protected function createModel(array $data)
    {
        $tab = parent::createModel($data);

        if (isset($data['alias']) && $this->normalizeValueAsBoolean($data['alias'])) {
            $tab->setGlobalTab(
                empty($data['service_name'])
                    ? $this->getGlobalTabByLink($data['link'])
                    : $this->getGlobalTabByServiceName($data['service_name'])
            );
        }

        return $tab;
    }

    /**
     * Update model
     *
     * @param \XLite\Model\AEntity $model Model
     * @param array                $data  Data
     *
     * @return boolean
     */
    protected function updateModel(\XLite\Model\AEntity $model, array $data)
    {
        $result = parent::updateModel($model, $data);

        if (!$model->isGlobal()) {
            $link = $data["link"] ?: $this->getRepository()->generateTabLink($model);
            $model->setLink($link);
        }

        return $result;
    }

    /**
     * Import data
     *
     * @param array $data Row set Data
     *
     * @return boolean
     */
    protected function importData(array $data)
    {
        $result = parent::importData($data);

        if ($result) {
            \XLite\Core\Database::getEM()->flush();
        }

        return $result;
    }

    // {{{ Verification

    /**
     * Get messages
     *
     * @return array
     */
    public static function getMessages()
    {
        return parent::getMessages() + [
            'TABS-PRODUCT-FMT'     => 'The product with SKU "{{value}}" does not exist. The tab "{{tab_name}}" will not be imported',
            'TABS-NAME-FMT'        => 'The name is empty',
            'TABS-ENABLED-FMT'     => 'Wrong enabled format',
            'TABS-POSITION-FMT'    => 'Wrong position format',
            'TABS-GLOBAL-NF'       => 'Global tab not found',
            'TABS-DUPLICATED-LINK' => 'Duplicated link',
        ];
    }

    /**
     * Verify 'name' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyName($value, array $column)
    {
        $value = $this->getDefLangValue($value);
        if ($this->verifyValueAsEmpty($value)) {
            $this->addError('TABS-NAME-FMT', ['column' => $column, 'value' => $value]);
        }
    }

    /**
     * Verify 'content' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyContent($value, array $column)
    {
    }

    /**
     * Verify 'position' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyPosition($value, array $column)
    {
        if (!$this->verifyValueAsEmpty($value) && !$this->verifyValueAsFloat($value)) {
            $this->addWarning('TABS-POSITION-FMT', ['column' => $column, 'value' => $value]);
        }
    }

    /**
     * Verify 'product' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyProduct($value, array $column)
    {
        $tabNames = $this->currentRowData['name'];
        $tabName = $this->getDefLangValue($tabNames);

        if (!$this->verifyValueAsEmpty($value) && !$this->verifyValueAsProduct($value)) {
            $this->addWarning('TABS-PRODUCT-FMT', ['column' => $column, 'value' => $value, 'tab_name' => $tabName]);
        }
    }

    /**
     * Verify 'enabled' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyEnabled($value, array $column)
    {
        if (!$this->verifyValueAsEmpty($value) && !$this->verifyValueAsBoolean($value)) {
            $this->addWarning('TABS-ENABLED-FMT', ['column' => $column, 'value' => $value]);
        }
    }

    /**
     * Verify complex value
     *
     * @param mixed $data   Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyGlobalTab($data, array $column)
    {
        if (isset($data['alias']) && $this->normalizeValueAsBoolean($data['alias'])) {
            $globalTab = empty($data['service_name'])
                ? $this->getGlobalTabByLink($data['link'])
                : $this->getGlobalTabByServiceName($data['service_name']);

            if (!$globalTab) {
                $this->addError('TABS-GLOBAL-NF', [
                    'column' => $column,
                    'value' => empty($data['service_name']) ? $data['link'] : $data['service_name']
                ]);
            }
        } elseif ($data['link']) {
            $qb = Database::getRepo('XLite\Model\Product\GlobalTab')->createQueryBuilder('gt');

            $qb->andWhere('gt.link = :link')
                ->setParameter('link', $data['link']);

            $globalTabWithSameLink = $qb->getSingleResult();
            if ($globalTabWithSameLink) {
                $this->addError('TABS-DUPLICATED-LINK', [
                    'column' => $column,
                    'value' => $data['link'],
                    'globalTab' => $globalTabWithSameLink->getName()
                ]);
            }
        }
    }

    // }}}

    // {{{ Normalizators

    /**
     * Normalize 'position' value
     *
     * @param mixed @value Value
     *
     * @return integer
     */
    protected function normalizePositionValue($value)
    {
        return $this->normalizeValueAsFloat($value);
    }

    /**
     * Normalize 'position' value
     *
     * @param mixed @value Value
     *
     * @return integer
     */
    protected function normalizeEnabledValue($value)
    {
        return $this->normalizeValueAsBoolean($value);
    }

    /**
     * Normalize 'product' value
     *
     * @param mixed @value Value
     *
     * @return \XLite\Model\ProductClass
     */
    protected function normalizeProductValue($value)
    {
        return $this->normalizeValueAsProduct($value);
    }

    // }}}

    // {{{ Import

    /**
     * Import column value
     *
     * @param Tab    $tab    Order
     * @param string $value  Value
     * @param array  $column Column info
     */
    protected function importNameColumn(Tab $tab, $value, array $column)
    {
        if (!$tab->isGlobal()) {
            $this->updateModelTranslations($tab, $value, 'name');
        }
    }

    /**
     * Import column value
     *
     * @param Tab    $tab    Order
     * @param string $value  Value
     * @param array  $column Column info
     */
    protected function importContentColumn(Tab $tab, $value, array $column)
    {
        if (!$tab->isGlobal()) {
            $this->updateModelTranslations($tab, $value, 'content');
        }
    }

    /**
     * Import column value
     *
     * @param Tab    $tab    Order
     * @param string $value  Value
     * @param array  $column Column info
     */
    protected function importBriefInfoColumn(Tab $tab, $value, array $column)
    {
        if (!$tab->isGlobal()) {
            $this->updateModelTranslations($tab, $value, 'brief_info');
        }
    }

    /**
     * Import column value
     *
     * @param Tab    $tab    Order
     * @param string $value  Value
     * @param array  $column Column info
     */
    protected function importAliasColumn(Tab $tab, $value, array $column)
    {
    }

    /**
     * Import column value
     *
     * @param Tab    $tab    Order
     * @param string $value  Value
     * @param array  $column Column info
     */
    protected function importServiceNameColumn(Tab $tab, $value, array $column)
    {
    }

    /**
     * Import column value
     *
     * @param Tab    $tab    Order
     * @param string $value  Value
     * @param array  $column Column info
     */
    protected function importGlobalTabColumn(Tab $tab, $value, array $column)
    {
    }

    // }}}
}