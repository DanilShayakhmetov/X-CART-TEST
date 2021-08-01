<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CustomProductTabs\Logic\Import\Processor;

use XLite\Core\Database;
use XLite\Model\Product\GlobalTab;

/**
 * GlobalTabs
 */
class GlobalTabs extends \XLite\Logic\Import\Processor\AProcessor
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
        return 0 === strpos($file->getFilename(), 'global-product-tabs');
    }

    /**
     * Get import file name format
     *
     * @return string
     */
    public function getFileNameFormat()
    {
        return 'global-product-tabs.csv';
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
        return Database::getRepo('XLite\Model\Product\GlobalTab');
    }

    /**
     * Define columns
     *
     * @return array
     */
    protected function defineColumns()
    {
        return [
            'name'         => [
                static::COLUMN_IS_MULTILINGUAL => true,
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
            'service_name' => [],
            'link'         => [
                static::COLUMN_IS_KEY          => true,
            ],
            'vname'        => [
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
    protected function detectVnameHeader(array $column, array $row)
    {
        return $this->detectHeaderByPattern('(name_[a-z]+|service_name)', $row);
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
        $qb = $this->getRepository()->createQueryBuilder('gt');

        if (!empty($data['link'])) {
            $qb->andWhere('gt.link = :link')
                ->setParameter('link', $data['link']);

            return $qb->getSingleResult();
        }

        if (!empty($data['service_name'])) {
            $qb->andWhere('gt.service_name = :service_name')
                ->setParameter('service_name', $data['service_name']);

            return $qb->getSingleResult();
        }
        
        return null;
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
        $globalTab = parent::createModel($data);

        $globalTab->setEnabled(false);

        if (empty($data['service_name'])) {
            $customTab = Database::getRepo('XLite\Module\XC\CustomProductTabs\Model\Product\CustomGlobalTab')->insert(null, false);
            $customTab->setGlobalTab($globalTab);
            $globalTab->setCustomTab($customTab);

            $this->updateModelTranslations($customTab, $data['name'], 'name');

            if (!empty($data['content'])) {
                $this->updateModelTranslations($customTab, $data['content'], 'content');
            }

            if (!empty($data['brief_info'])) {
                $this->updateModelTranslations($customTab, $data['brief_info'], 'brief_info');
            }

            $customTab->assignLink();
        } else {
            $globalTab->setServiceName($data['service_name']);
        }

        return $globalTab;
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
//        unset($data['link']);

        return parent::updateModel($model, $data);
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
            'TABS-NAME-FMT'     => 'The name is empty',
            'TABS-ENABLED-FMT'  => 'Wrong enabled format',
            'TABS-POSITION-FMT' => 'Wrong position format',
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
    protected function verifyVname($value, array $column)
    {
        if (empty($value['service_name']) && empty($value['name_' . \XLite\Logic\Import\Importer::getLanguageCode()])) {
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

    // }}}

    // {{{ Import

    /**
     * Import column value
     *
     * @param GlobalTab $tab    Order
     * @param array    $value  Value
     * @param array     $column Column info
     */
    protected function importNameColumn(GlobalTab $tab, $value, array $column)
    {
        if ($tab->getCustomTab()) {
            $this->updateModelTranslations($tab->getCustomTab(), $value, 'name');
        }
    }

    /**
     * Import column value
     *
     * @param GlobalTab $tab    Order
     * @param array    $value  Value
     * @param array     $column Column info
     */
    protected function importContentColumn(GlobalTab $tab, $value, array $column)
    {
        if ($tab->getCustomTab()) {
            $this->updateModelTranslations($tab->getCustomTab(), $value, 'content');
        }
    }

    /**
     * Import column value
     *
     * @param GlobalTab $tab    Order
     * @param array    $value  Value
     * @param array     $column Column info
     */
    protected function importBriefInfoColumn(GlobalTab $tab, $value, array $column)
    {
        if ($tab->getCustomTab()) {
            foreach ($value as $code => $line) {
                if ($line === static::NULL_VALUE) {
                    $value[$code] = '';
                }
            }

            $this->updateModelTranslations($tab->getCustomTab(), $value, 'brief_info');
        }
    }

    /**
     * Import column value
     *
     * @param GlobalTab $tab    Order
     * @param string    $value  Value
     * @param array     $column Column info
     */
    protected function importServiceNameColumn(GlobalTab $tab, $value, array $column)
    {
        $tab->setServiceName(trim($value) ?: null);
    }

    /**
     * Import column value
     *
     * @param GlobalTab $tab    Order
     * @param array    $value  Value
     * @param array     $column Column info
     */
    protected function importLinkColumn(GlobalTab $tab, $value, array $column)
    {
    }

    // }}}
}