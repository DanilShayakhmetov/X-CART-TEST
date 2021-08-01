<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Logic\Import\Processor;

/**
 * Categories import processor
 */
class Categories extends \XLite\Logic\Import\Processor\AProcessor
{
    /**
     * Get title
     *
     * @return string
     */
    public static function getTitle()
    {
        return static::t('Categories imported');
    }

    /**
     * Mark all images as processed
     *
     * @return void
     */
    public function markAllImagesAsProcessed()
    {
        \XLite\Core\Database::getRepo('XLite\Model\Image\Category\Image')->unmarkAsProcessed();
        \XLite\Core\Database::getRepo('XLite\Model\Image\Category\Banner')->unmarkAsProcessed();
    }

    /**
     * Get repository
     *
     * @return \XLite\Model\Repo\ARepo
     */
    protected function getRepository()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Category');
    }

    /**
     * Initialize processor
     *
     * @return void
     */
    protected function initialize()
    {
        parent::initialize();

        $this->importer->enableCategoriesStructureCorrection();
        $this->importer->enableImageResize();
    }

    // {{{ Columns

    /**
     * Define columns
     *
     * @return array
     */
    protected function defineColumns()
    {
        return [
            'categoryId'   => [
                static::COLUMN_IS_KEY => true,
            ],
            'path'         => [
                static::COLUMN_IS_KEY => true,
            ],
            'identity'     => [
                static::COLUMN_IS_MULTICOLUMN  => true,
                static::COLUMN_HEADER_DETECTOR => true,
                static::COLUMN_IS_IMPORT_EMPTY => true,
            ],
            'enabled'      => [],
            'showTitle'    => [],
            'position'     => [],
            'memberships'  => [
                static::COLUMN_IS_MULTIPLE => true
            ],
            'image'        => [],
            'banner'       => [],
            'cleanURL'     => [
                static::COLUMN_LENGTH => 255,
            ],
            'name'         => [
                static::COLUMN_IS_MULTILINGUAL => true,
                static::COLUMN_LENGTH          => 255,
            ],
            'description'  => [
                static::COLUMN_IS_MULTILINGUAL => true,
                static::COLUMN_IS_TAGS_ALLOWED => true,
            ],
            'metaTags'     => [
                static::COLUMN_IS_MULTILINGUAL => true,
                static::COLUMN_LENGTH          => 255,
            ],
            'metaDescType' => [],
            'metaDesc'    => [
                static::COLUMN_IS_MULTILINGUAL => true,
            ],
            'metaTitle'    => [
                static::COLUMN_IS_MULTILINGUAL => true,
                static::COLUMN_LENGTH          => 255,
            ],
        ];
    }

    /**
     * Detect header(s)
     *
     * @param array $column Column info
     * @param array $row Header row
     *
     * @return array
     */
    protected function detectIdentityHeader(array $column, array $row)
    {
        $pattern = "(categoryId|path|name)";

        return $this->detectHeaderByPattern($pattern, $row, true);
    }

    // }}}

    // {{{ Column metadata

    /**
     * Get value for default language, allow to get non-default value right from file
     *
     * @param mixed $value Value
     *
     * @return mixed
     */
    protected function getDefLangValue($value)
    {
        $parent_value = parent::getDefLangValue($value);
        if (is_null($parent_value) && is_array($value) && !empty($value)) {
            $parent_value = array_values($value)[0];
        }

        return $parent_value;
    }
    // }}}

    // {{{ Verification

    /**
     * Get messages
     *
     * @return array
     */
    public static function getMessages()
    {
        return parent::getMessages()
            + [
                'CATEGORY-ENABLED-FMT'             => 'Wrong enabled format',
                'CATEGORY-SHOW-TITLE-FMT'          => 'Wrong show title format',
                'CATEGORY-POSITION-FMT'            => 'Wrong position format',
                'CATEGORY-NAME-FMT'                => 'The name is empty',
                'CATEGORY-IMG-LOAD-FAILED'         => 'Error of image loading. Make sure the "images" directory has write permissions.',
                'CATEGORY-IMG-FILE-LOAD-FAILED'    => 'Failed to load the file {{value}} because it does not exist',
                'CATEGORY-IMG-URL-LOAD-FAILED'     => "Couldn't download the image {{value}} from URL",
                'CATEGORY-BANNER-LOAD-FAILED'      => 'Error of banner loading. Make sure the "images" directory has write permissions.',
                'CATEGORY-BANNER-FILE-LOAD-FAILED' => 'Failed to load the banner {{value}} because it does not exist',
                'CATEGORY-BANNER-URL-LOAD-FAILED'  => "Couldn't download the banner {{value}} from URL",
                'CATEGORY-CATEGORY-ID-NF'          => 'Category with id X not found, new category will be created',
                'CATEGORY-IDENTITY-FMT'            => 'Category id or path is required',
                'CATEGORY-PATH-NAME-FMT'           => 'Last element of category path should be same as name',
                'CATEGORY-PATH-COLLATION'          => 'Category path contains invalid symbols',
                'CATEGORY-NAME-COLLATION'          => 'Category name contains invalid symbols',
                'CATEGORY-META-DESC-TYPE-FMT'      => 'Wrong meta desc type format',
            ];
    }

    /**
     * Returns csv format manual URL
     *
     * @return string
     */
    public static function getCSVFormatManualURL()
    {
        return static::t('https://kb.x-cart.com/import-export/csv_format_by_x-cart_data_type/csv_import_categories.html');
    }

    /**
     * Verify 'identity' value
     *
     * @param mixed $value Value
     * @param array $column Column info
     */
    protected function verifyIdentity($value, array $column)
    {
        if (!empty($value['categoryId'])) {
            if (!$this->getRepository()->find((integer)$value['categoryId'])) {
                $this->addWarning('CATEGORY-CATEGORY-ID-NF', ['column' => $column, 'value' => (integer)$value['categoryId']]);
            }
        } elseif (isset($value['path'])) {
            $model = $this->getCategoryByPath($value['path'], false);

            if (!$this->verifyValueAsUtf8mb3($value['path'])) {
                $this->addError('CATEGORY-PATH-COLLATION', ['column' => $column]);
            }

            if (!$model) {
                $path = explode('>>>', $value['path']);
                $lastElement = count($path) ? trim(array_pop($path)) : '';

                $nameKey = isset($value['name']) ? 'name' : 'name_' . $this->importer->getLanguageCode();
                if (
                    !isset($value[$nameKey])
                    || trim($value[$nameKey]) !== $lastElement
                ) {
                    $this->addWarning('CATEGORY-PATH-NAME-FMT', ['column' => $column]);
                }
            }
        } else {
            $this->addWarning('CATEGORY-IDENTITY-FMT', ['column' => $column]);
        }
    }

    /**
     * Verify 'enabled' value
     *
     * @param mixed $value Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyEnabled($value, array $column)
    {
        if (!$this->verifyValueAsEmpty($value) && !$this->verifyValueAsBoolean($value)) {
            $this->addWarning('CATEGORY-ENABLED-FMT', ['column' => $column, 'value' => $value]);
        }
    }

    /**
     * Verify 'show title' value
     *
     * @param mixed $value Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyShowTitle($value, array $column)
    {
        if (!$this->verifyValueAsEmpty($value) && !$this->verifyValueAsBoolean($value)) {
            $this->addWarning('CATEGORY-SHOW-TITLE-FMT', ['column' => $column, 'value' => $value]);
        }
    }

    /**
     * Verify 'position' value
     *
     * @param mixed $value Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyPosition($value, array $column)
    {
        if (!$this->verifyValueAsEmpty($value) && !$this->verifyValueAsUinteger($value)) {
            $this->addWarning('CATEGORY-POSITION-FMT', ['column' => $column, 'value' => $value]);
        }
    }

    /**
     * Verify 'memberships' value
     *
     * @param mixed $value Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyMemberships($value, array $column)
    {
        if (!$this->verifyValueAsEmpty($value) && !$this->verifyValueAsNull($value)) {
            foreach ($value as $membership) {
                if (!$this->verifyValueAsEmpty($membership) && !$this->verifyValueAsMembership($membership)) {
                    $this->addWarning('GLOBAL-MEMBERSHIP-FMT', ['column' => $column, 'value' => $membership]);
                }
            }
        }
    }

    /**
     * Verify 'image' value
     *
     * @param mixed $value Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyImage($value, array $column)
    {
        if (!$this->verifyValueAsEmpty($value) && !$this->verifyValueAsFile($value)) {
            if ($this->verifyValueAsURL($value)) {
                $this->addWarning('CATEGORY-IMG-URL-LOAD-FAILED', ['column' => $column, 'value' => $value]);
            } else {
                $this->addWarning('GLOBAL-IMAGE-FMT', ['column' => $column, 'value' => $value]);
            }
        }
    }

    /**
     * Verify 'banner' value
     *
     * @param mixed $value Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyBanner($value, array $column)
    {
        if (!$this->verifyValueAsEmpty($value) && !$this->verifyValueAsFile($value)) {
            if ($this->verifyValueAsURL($value)) {
                $this->addWarning('CATEGORY-BANNER-URL-LOAD-FAILED', ['column' => $column, 'value' => $value]);
            } else {
                $this->addWarning('GLOBAL-IMAGE-FMT', ['column' => $column, 'value' => $value]);
            }
        }
    }

    /**
     * Verify 'clean URL' value
     *
     * @param mixed $value Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyCleanURL($value, array $column)
    {
    }

    /**
     * Verify 'name' value
     *
     * @param mixed $value Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyName($value, array $column)
    {
        $value = $this->getDefLangValue($value);

        if ($this->verifyValueAsEmpty($value)) {
            $this->addError('CATEGORY-NAME-FMT', ['column' => $column, 'value' => $value]);
        } else if (!$this->verifyValueAsUtf8mb3($value)) {
            $this->addError('CATEGORY-NAME-COLLATION', ['column' => $column]);
        }
    }

    /**
     * Verify 'description' value
     *
     * @param mixed $value Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyDescription($value, array $column)
    {
    }

    /**
     * Verify 'meta tags' value
     *
     * @param mixed $value Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyMetaTags($value, array $column)
    {
    }

    /**
     * Verify 'meta desc type' value
     *
     * @param mixed $value Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyMetaDescType($value, array $column)
    {
        if (!$this->verifyValueAsEmpty($value) && !$this->verifyValueAsMetaTagsType($value)) {
            $this->addWarning('CATEGORY-META-DESC-TYPE-FMT', ['column' => $column, 'value' => $value]);
        }
    }

    /**
     * Verify 'meta desc' value
     *
     * @param mixed $value Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyMetaDesc($value, array $column)
    {
    }

    /**
     * Verify 'meta title' value
     *
     * @param mixed $value Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyMetaTitle($value, array $column)
    {
    }

    // }}}

    // {{{ Normalizators

    /**
     * Normalize 'enabled' value
     *
     * @param mixed @value Value
     *
     * @return boolean
     */
    protected function normalizeEnabledValue($value)
    {
        return $this->normalizeValueAsBoolean($value);
    }

    /**
     * Normalize 'show title' value
     *
     * @param mixed @value Value
     *
     * @return boolean
     */
    protected function normalizeShowTitleValue($value)
    {
        return $this->normalizeValueAsBoolean($value);
    }

    /**
     * Normalize 'position' value
     *
     * @param mixed @value Value
     *
     * @return integer
     */
    protected function normalizePositionValue($value)
    {
        return abs((int)($value));
    }

    // }}}

    // {{{ Import

    /**
     * Import data
     *
     * @param array $data Row set Data
     *
     * @return boolean
     */
    protected function importData(array $data)
    {
        \Xlite\Core\Database::getRepo('XLite\Model\Product')->setBlockQuickDataFlag(true);

        return parent::importData($data);
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
        if (!empty($data['categoryId'])) {
            $model = $this->getRepository()->find((integer)$data['categoryId']);
        }

        if (!isset($model) || !$model) {
            $path = isset($data['path']) ? $data['path'] : '';
            $model = $this->getCategoryByPath($path, false);
        }

        return $model;
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
        $path = isset($data['path']) ? $data['path'] : '';
        $path = explode('>>>', $path);
        $lastElement = count($path) ? trim(array_pop($path)) : '';
        $name = trim($this->getDefLangValue($data['name']));

        if ($name !== $lastElement) {
            return null;
        }

        return parent::createModel($data);
    }

    /**
     * Import 'categoryId' value
     *
     * @param \XLite\Model\Category $model Category
     * @param string $value Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function importCategoryIdColumn(\XLite\Model\Category $model, $value, array $column)
    {
    }

    /**
     * Import 'path' value
     *
     * @param \XLite\Model\Category $model Category
     * @param string $value Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function importPathColumn(\XLite\Model\Category $model, $value, array $column)
    {
        if ($model->isRootCategory()) {
            return;
        }

        if (!is_array($value)) {
            $path = array_map('trim', explode('>>>', $value));
        }

        $cacheKey = implode('/', $path);
        $model->setName(array_pop($path));
        $model->setParent($this->addCategoryByPath($path));
        if (!$model->isPersistent()) {
            \XLite\Core\Database::getEM()->persist($model);
            \XLite\Core\Database::getEM()->flush();
        }

        $this->setCategoryByPathCache($cacheKey, $model);
    }

    /**
     * Import 'memberships' value
     *
     * @param \XLite\Model\Category $model Category
     * @param array $value Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function importMembershipsColumn(\XLite\Model\Category $model, array $value, array $column)
    {
        if ($value) {
            if ($model->getMemberships()) {
                foreach ($model->getMemberships() as $membership) {
                    $membership->getCategories()->removeElement($model);
                }
                $model->getMemberships()->clear();
            }

            if (!$this->verifyValueAsNull($value)) {
                foreach ($value as $membership) {
                    $membership = $this->normalizeValueAsMembership($membership);
                    if ($membership) {
                        $model->addMemberships($membership);
                        $membership->addCategory($model);
                    }
                }
            }
        }
    }

    /**
     * Import 'image' value
     *
     * @param \XLite\Model\Category $model Category
     * @param string $value Value
     * @param array $column Column info
     *
     * @return void
     * @throws \Exception
     */
    protected function importImageColumn(\XLite\Model\Category $model, $value, array $column)
    {
        if (!$value) {
            return;
        }

        if ($this->verifyValueAsNull($value)) {
            if ($model->getImage()) {
                \XLite\Core\Database::getEM()->remove($model->getImage());
                $model->setImage();
                \XLite\Core\Database::getEM()->flush();
            }
        } else {
            $image = $model->getImage();

            if (!$image) {
                $image = new \XLite\Model\Image\Category\Image;
            }

            $success = $image->loadFromPath($value);

            if ($success) {
                \XLite\Core\Database::getEM()->persist($image);
                $image->setNeedProcess(1);
                $image->setCategory($model);
                $model->setImage($image);
            } else {
                $file = $this->verifyValueAsLocalURL($value) ? $this->getLocalPathFromURL($value) : $value;
                if ($image->getLoadError() === 'unwriteable') {
                    $this->addError('CATEGORY-IMG-LOAD-FAILED', [
                        'column' => $column,
                        'value'  => $this->verifyValueAsURL($file) ? $value : LC_DIR_ROOT . $file
                    ]);
                } elseif ($image->getLoadError() === 'nonexistent') {
                    $this->addWarning('CATEGORY-IMG-FILE-LOAD-FAILED', [
                        'column' => $column,
                        'value'  => $this->verifyValueAsURL($file) ? $value : LC_DIR_ROOT . $file
                    ]);
                } elseif ($image->getLoadError()) {
                    $this->addWarning('CATEGORY-IMG-URL-LOAD-FAILED', [
                        'column' => $column,
                        'value'  => $this->verifyValueAsURL($file) ? $value : LC_DIR_ROOT . $file
                    ]);
                }
            }
        }
    }

    /**
     * Import 'banner' value
     *
     * @param \XLite\Model\Category $model Category
     * @param string $value Value
     * @param array $column Column info
     *
     * @return void
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     * @throws \Exception
     */
    protected function importBannerColumn(\XLite\Model\Category $model, $value, array $column)
    {
        if (!$value) {
            return;
        }

        if ($this->verifyValueAsNull($value)) {
            if ($model->getBanner()) {
                \XLite\Core\Database::getEM()->remove($model->getBanner());
                $model->setBanner(null);
                \XLite\Core\Database::getEM()->flush();
            }
        } else {
            $path = $value;

            $image = $model->getBanner();

            $file = $this->verifyValueAsLocalURL($path) ? $this->getLocalPathFromURL($path) : $path;

            if (!$image) {
                $image = new \XLite\Model\Image\Category\Banner;
            }

            $success = $image->loadFromPath($path);

            if ($success) {
                \XLite\Core\Database::getEM()->persist($image);
                $image->setNeedProcess(1);
                $image->setCategory($model);
                $model->setBanner($image);
            } else {
                if ($image->getLoadError() === 'unwriteable') {
                    $this->addError('CATEGORY-BANNER-LOAD-FAILED', [
                        'column' => $column,
                        'value'  => $this->verifyValueAsURL($file) ? $path : LC_DIR_ROOT . $file
                    ]);
                } elseif ($image->getLoadError() === 'nonexistent') {
                    $this->addWarning('CATEGORY-BANNER-FILE-LOAD-FAILED', [
                        'column' => $column,
                        'value'  => $this->verifyValueAsURL($file) ? $value : LC_DIR_ROOT . $file
                    ]);
                } elseif ($image->getLoadError()) {
                    $this->addWarning('CATEGORY-BANNER-URL-LOAD-FAILED', [
                        'column' => $column,
                        'value'  => $this->verifyValueAsURL($file) ? $path : LC_DIR_ROOT . $file
                    ]);
                }
            }
        }
    }

    /**
     * Import 'cleanURL' value
     *
     * @param \XLite\Model\Category $model Category
     * @param string $value Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function importCleanURLColumn(\XLite\Model\Category $model, $value, array $column)
    {
        $this->updateCleanURL($model, $value);
    }

    // }}}
}
