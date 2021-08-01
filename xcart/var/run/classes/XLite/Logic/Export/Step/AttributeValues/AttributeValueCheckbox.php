<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Logic\Export\Step\AttributeValues;

/**
 * Products attribute values: checkboxes
 */
class AttributeValueCheckbox extends \XLite\Logic\Export\Step\AttributeValues\AAttributeValues
{
    /**
     * Get repository
     *
     * @return \XLite\Model\Repo\ARepo
     */
    protected function getRepository()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\AttributeValue\AttributeValueCheckbox');
    }

    /**
     * Get column value for 'value' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getValueColumnValue(array $dataset, $name, $i)
    {
        return $dataset['model']->getValue() ? 'Yes' : 'No';
    }
}
