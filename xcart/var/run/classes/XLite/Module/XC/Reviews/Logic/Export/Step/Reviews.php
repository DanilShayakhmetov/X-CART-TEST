<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Reviews\Logic\Export\Step;

/**
 * Reviews
 */
class Reviews extends \XLite\Logic\Export\Step\AStep
{
    // {{{ Data

    /**
     * Get repository
     *
     * @return \XLite\Model\Repo\ARepo
     */
    protected function getRepository()
    {
        return \XLite\Core\Database::getRepo('XLite\Module\XC\Reviews\Model\Review');
    }


    /**
     * Get filename
     *
     * @return string
     */
    protected function getFilename()
    {
        return 'reviews.csv';
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
            'review'       => [],
            'response'     => [],
            'rating'       => [],
            'additionDate' => [],
            'responseDate' => [],
            'respondent'   => [],
            'reviewerName' => [],
            'email'        => [],
            'status'       => [],
            'useForMeta'   => [],
        ];

        return $columns;
    }

    // }}}

    // {{{ Getters and formatters

    /**
     * Get column value for 'product' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getProductColumnValue(array $dataset, $name, $i)
    {
        $product = $dataset['model']->getProduct();
        $sku = $product->getSKU();

        return $sku ?: 'Unknown';
    }

    /**
     * Get column value for 'review' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getReviewColumnValue(array $dataset, $name, $i)
    {
        return $this->getColumnValueByName($dataset['model'], 'review');
    }

    /**
     * Get column value for 'response' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getResponseColumnValue(array $dataset, $name, $i)
    {
        return $this->getColumnValueByName($dataset['model'], 'response');
    }

    /**
     * Get column value for 'rating' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getRatingColumnValue(array $dataset, $name, $i)
    {
        return $this->getColumnValueByName($dataset['model'], 'rating');
    }

    /**
     * Get column value for 'additionDate' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getAdditionDateColumnValue(array $dataset, $name, $i)
    {
        return $this->getColumnValueByName($dataset['model'], 'additionDate');
    }

    /**
     * Get column value for 'responseDate' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getResponseDateColumnValue(array $dataset, $name, $i)
    {
        return $this->formatTimestamp($this->getColumnValueByName($dataset['model'], 'responseDate'));
    }

    /**
     * Get column value for 'respondent' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getRespondentColumnValue(array $dataset, $name, $i)
    {
        $profile = $this->getColumnValueByName($dataset['model'], 'respondent');

        if ($profile instanceof \XLite\Model\Profile) {
            return $profile->getLogin();
        }

        return '';
    }

    /**
     * Format 'additionDate' field value
     *
     * @param mixed  $value   Value
     * @param array  $dataset Dataset
     * @param string $name    Column name
     *
     * @return string
     */
    protected function formatAdditionDateColumnValue($value, array $dataset, $name)
    {
        return $this->formatTimestamp($value);
    }

    /**
     * Get column value for 'reviewerName' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getReviewerNameColumnValue(array $dataset, $name, $i)
    {
        return $this->getColumnValueByName($dataset['model'], 'reviewerName');
    }

    /**
     * Get column value for 'email' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getEmailColumnValue(array $dataset, $name, $i)
    {
        return $this->getColumnValueByName($dataset['model'], 'email');
    }

    /**
     * Get column value for 'status' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getStatusColumnValue(array $dataset, $name, $i)
    {
        $status = $dataset['model']->isApproved();

        return $status ? 'Approved' : 'Pending';
    }

    /**
     * Get column value for 'useForMeta' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getUseForMetaColumnValue(array $dataset, $name, $i)
    {
        return $this->getColumnValueByName($dataset['model'], 'useForMeta');
    }

    // }}}
}
