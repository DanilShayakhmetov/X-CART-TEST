<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\GoSocial\Logic\Export\Step;

/**
 * Products
 */
abstract class Products extends \XLite\Logic\Export\Step\Products implements \XLite\Base\IDecorator
{
    /**
     * Define columns
     *
     * @return array
     */
    protected function defineColumns()
    {
        $columns = parent::defineColumns();

        $columns['useCustomOpenGraphMeta'] = array();

        foreach ($this->getUsedLanguageCodes() as $code) {
            $columns['openGraphMeta_' . $code] = [
                static::COLUMN_GETTER => 'getOpenGraphMetaColumnValue',
            ];
        }

        return $columns;
    }

    /**
     * Get column value for 'openGraphMeta' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getOpenGraphMetaColumnValue(array $dataset, $name, $i)
    {
        $isUseCustomOpenGraphMeta = $this->getColumnValueByName($dataset['model'], 'useCustomOG');

        return $isUseCustomOpenGraphMeta
            ? $dataset['model']->getTranslation(substr($name, -2))->getterProperty('ogMeta')
            : '';
    }

    /**
     * Get column value for 'useCustomOpenGraphMeta' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getUseCustomOpenGraphMetaColumnValue(array $dataset, $name, $i)
    {
        return $this->getColumnValueByName($dataset['model'], 'useCustomOG');
    }

}
