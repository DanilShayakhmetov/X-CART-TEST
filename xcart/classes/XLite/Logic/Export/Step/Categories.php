<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Logic\Export\Step;

/**
 * Categories
 */
class Categories extends \XLite\Logic\Export\Step\Base\I18n
{
    // {{{ Data

    /**
     * Get repository
     *
     * @return \XLite\Model\Repo\ARepo
     */
    protected function getRepository()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Category');
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
            'categoryId'  => [],
            'path'        => [],
            'enabled'     => [],
            'showTitle'   => [],
            'position'    => [],
            'memberships' => [],
            'image'       => [],
            'banner'       => [],
            'cleanURL'    => [],
            'metaDescType' => [],
        ];

        $columns += $this->assignI18nColumns(
            [
                'name'        => [],
                'description' => [],
                'metaTags'    => [],
                'metaDesc'    => [],
                'metaTitle'   => [],
            ]
        );

        return $columns;
    }

    // }}}

    // {{{ Getters and formatters

    /**
     * Get column value for 'path' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getCategoryIdColumnValue(array $dataset, $name, $i)
    {
        return $dataset['model']->getCategoryId();
    }

    /**
     * Get column value for 'path' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getPathColumnValue(array $dataset, $name, $i)
    {
        $result = [];
        foreach ($this->getRepository()->getCategoryPath($dataset['model']->getCategoryId()) as $category) {
            $result[] = $category->getName();
        }

        return implode(' >>> ', $result);
    }

    /**
     * Get column value for 'enabled' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getEnabledColumnValue(array $dataset, $name, $i)
    {
        return $this->getColumnValueByName($dataset['model'], 'enabled');
    }

    /**
     * Get column value for 'showTitle' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getShowTitleColumnValue(array $dataset, $name, $i)
    {
        return $this->getColumnValueByName($dataset['model'], 'show_title');
    }

    /**
     * Get column value for 'position' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getPositionColumnValue(array $dataset, $name, $i)
    {
        return $this->getColumnValueByName($dataset['model'], 'pos');
    }

    /**
     * Get column value for 'memberships' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getMembershipsColumnValue(array $dataset, $name, $i)
    {
        $result = [];

        foreach ($dataset['model']->getMemberships() as $membership) {
            $result[] = $membership->getName();
        }

        return $result;
    }

    /**
     * Get column value for 'image' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getImageColumnValue(array $dataset, $name, $i)
    {
        return $dataset['model']->getImage();
    }

    /**
     * Get column value for 'banner' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getBannerColumnValue(array $dataset, $name, $i)
    {
        return $dataset['model']->getBanner();
    }

    /**
     * Get column value for 'cleanUrl' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getCleanUrlColumnValue(array $dataset, $name, $i)
    {
        return $this->getColumnValueByName($dataset['model'], 'cleanUrl');
    }

    /**
     * Get column value for 'metaDescType' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getMetaDescTypeColumnValue(array $dataset, $name, $i)
    {
        return $this->getColumnValueByName($dataset['model'], 'metaDescType') ?: 'A';
    }

    /**
     * Copy resource
     *
     * @param \XLite\Model\Base\Storage $storage      Storage
     * @param string                    $subdirectory Subdirectory
     *
     * @return boolean
     */
    protected function copyResource(\XLite\Model\Base\Storage $storage, $subdirectory)
    {
        if ($storage instanceOf \XLite\Model\Image\Category\Image) {
            $subdirectory .= LC_DS . 'categories';
        } elseif ($storage instanceOf \XLite\Model\Image\Category\Banner) {
            $subdirectory .= LC_DS . 'banners';
        }

        return parent::copyResource($storage, $subdirectory);
    }

    // }}}

}
