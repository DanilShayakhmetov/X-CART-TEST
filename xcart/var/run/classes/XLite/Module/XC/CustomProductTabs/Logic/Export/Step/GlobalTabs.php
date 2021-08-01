<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CustomProductTabs\Logic\Export\Step;

use XLite\Core\Database;

/**
 * GlobalTabs
 */
class GlobalTabs extends \XLite\Logic\Export\Step\Base\I18n
{
    // {{{ Data

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
     * Get filename
     *
     * @return string
     */
    protected function getFilename()
    {
        return 'global-product-tabs.csv';
    }

    // }}}

    // {{{ Columns

    /**
     * Define columns
     *
     * @return array
     */
    protected function defineColumns()
    {
        $columns = [
            'enabled'      => [],
            'position'     => [],
            'service_name' => [
                static::COLUMN_GETTER => 'getServiceNameColumnValue'
            ],
            'link'         => [
                static::COLUMN_GETTER => 'getLinkColumnValue'
            ],
        ];

        $columns += $this->assignI18nColumns([
            'name'       => [
                static::COLUMN_GETTER => 'getNameColumnValue'
            ],
            'content'    => [
                static::COLUMN_GETTER => 'getContentColumnValue'
            ],
            'brief_info' => [
                static::COLUMN_GETTER => 'getBriefInfoColumnValue'
            ],
        ]);

        return $columns;
    }

    /**
     * Get translation repository
     *
     * @return \XLite\Model\Repo\Base\Translation
     */
    protected function getTranslationRepository()
    {
        return Database::getRepo('XLite\Module\XC\CustomProductTabs\Model\Product\CustomGlobalTab')->getTranslationRepository();
    }

    /**
     * Get column value for tabs
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return boolean
     */
    protected function getEnabledColumnValue(array $dataset, $name, $i)
    {
        return $dataset['model']->getEnabled();
    }

    /**
     * Get column value for tabs
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return integer
     */
    protected function getPositionColumnValue(array $dataset, $name, $i)
    {
        return $dataset['model']->getPosition();
    }

    /**
     * Get column value for tabs
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return integer
     */
    protected function getServiceNameColumnValue(array $dataset, $name, $i)
    {
        return $dataset['model']->getServiceName();
    }

    /**
     * Get column value for tabs
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return integer
     */
    protected function getLinkColumnValue(array $dataset, $name, $i)
    {
        return $dataset['model']->getLink();
    }

    /**
     * Get column value for tabs
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return integer
     */
    protected function getNameColumnValue(array $dataset, $name, $i)
    {
        if ($dataset['model']->getCustomTab()) {
            return $dataset['model']->getCustomTab()->getTranslation(substr($name, -2))->getterProperty(substr($name, 0, -3));
        }

        return null;
    }

    /**
     * Get column value for tabs
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return integer
     */
    protected function getContentColumnValue(array $dataset, $name, $i)
    {
        if ($dataset['model']->getCustomTab()) {
            return $dataset['model']->getCustomTab()->getTranslation(substr($name, -2))->getterProperty(substr($name, 0, -3));
        }

        return '';
    }

    /**
     * Get column value for tabs
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return integer
     */
    protected function getBriefInfoColumnValue(array $dataset, $name, $i)
    {
        if ($dataset['model']->getCustomTab() && $dataset['model']->getCustomTab()->getTranslation(substr($name, -2), true)) {
            return $dataset['model']->getCustomTab()->getTranslation(substr($name, -2))->getterProperty(substr($name, 0, -3))
                ?: '';
        }

        return null;
    }
}