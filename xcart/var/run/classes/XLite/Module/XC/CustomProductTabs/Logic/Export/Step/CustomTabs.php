<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CustomProductTabs\Logic\Export\Step;

/**
 * CustomTabs
 */
class CustomTabs extends \XLite\Logic\Export\Step\Base\I18n
{
    // {{{ Data

    /**
     * Get repository
     *
     * @return \XLite\Model\Repo\ARepo
     */
    protected function getRepository()
    {
        return \XLite\Core\Database::getRepo('XLite\Module\XC\CustomProductTabs\Model\Product\Tab');
    }

    /**
     * Get used language codes
     *
     * @return array
     */
    protected function getUsedLanguageCodes()
    {
        $codes = parent::getUsedLanguageCodes();

        if (empty($codes)) {
            $codes = \XLite\Core\Database::getRepo('XLite\Module\XC\CustomProductTabs\Model\Product\CustomGlobalTab')
                ->getTranslationRepository()->getUsedLanguageCodes();
        }

        return $codes;
    }

    /**
     * Get filename
     *
     * @return string
     */
    protected function getFilename()
    {
        return 'product-custom-tabs.csv';
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
            'product'      => [],
            'enabled'      => [],
            'position'     => [],
            'alias'        => [],
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
            'content'    => [],
            'brief_info' => [
                static::COLUMN_GETTER => 'getBriefInfoColumnValue'
            ],
        ]);

        return $columns;
    }

    /**
     * Get column value for tabs
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getProductColumnValue(array $dataset, $name, $i)
    {
        return $dataset['model']->getProduct()
            ? $dataset['model']->getProduct()->getSku()
            : '';
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
    protected function getAliasColumnValue(array $dataset, $name, $i)
    {
        return $dataset['model']->isGlobal();
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
        return $dataset['model']->isGlobalCustom()
            ? $dataset['model']->getGlobalTab()->getLink()
            : $dataset['model']->getLink();
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
        if ($dataset['model']->isGlobalCustom()) {
            return $dataset['model']->getGlobalTab()->getCustomTab()->getTranslation(substr($name, -2))->getterProperty(substr($name, 0, -3));
        } elseif ($dataset['model']->isGlobalStatic()) {
            return \XLite\Core\Translation::getInstance()->translate($dataset['model']->getGlobalTab()->getServiceName(), [], substr($name, -2));
        }

        return parent::getTranslationColumnValue($dataset, $name, $i);
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
        if ($dataset['model']->getTranslation(substr($name, -2), true)) {
            return parent::getTranslationColumnValue($dataset, $name, $i)
                ?: '';
        }

        return null;
    }
}