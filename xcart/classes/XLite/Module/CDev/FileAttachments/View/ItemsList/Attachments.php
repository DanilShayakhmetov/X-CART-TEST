<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\FileAttachments\View\ItemsList;

/**
 * Attachments items list
 */
class Attachments extends \XLite\View\ItemsList\Model\Table
{
    /**
     * Default value for PARAM_WRAP_WITH_FORM
     *
     * @return boolean
     */
    protected function wrapWithFormByDefault()
    {
        return true;
    }

    /**
     * Get wrapper form target
     *
     * @return array
     */
    protected function getFormTarget()
    {
        return 'product';
    }

    /**
     * Get wrapper form action
     *
     * @return array
     */
    protected function getFormAction()
    {
        return 'update_attachments';
    }

    /**
     * Get wrapper form params
     *
     * @return array
     */
    protected function getFormParams()
    {
        return parent::getFormParams() + [
            'product_id' => $this->getProduct()->getId()
        ];
    }

    /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
        return [
            'title'       => [
                static::COLUMN_NAME    => \XLite\Core\Translation::lbl('File title'),
                static::COLUMN_CLASS   => 'XLite\Module\CDev\FileAttachments\View\FormField\Inline\Input\Text\File',
                static::COLUMN_PARAMS => [
                    \XLite\View\FormField\Input\Base\StringInput::PARAM_MAX_LENGTH => \XLite\Core\Database::getRepo('XLite\Module\CDev\FileAttachments\Model\Product\AttachmentTranslation')->getFieldInfo('title', 'length'),
                ],
                static::COLUMN_ORDERBY => 100,
            ],
            'description' => [
                static::COLUMN_NAME    => \XLite\Core\Translation::lbl('Description'),
                static::COLUMN_CLASS   => 'XLite\Module\CDev\FileAttachments\View\FormField\Inline\Textarea\Description',
                static::COLUMN_ORDERBY => 200,
                static::COLUMN_MAIN    => true,
            ],
            'access'      => [
                static::COLUMN_NAME    => \XLite\Core\Translation::lbl('Membership'),
                static::COLUMN_CLASS   => 'XLite\Module\CDev\FileAttachments\View\FormField\Inline\Select\AttributeMembership',
                static::COLUMN_ORDERBY => 300,
            ],
        ];
    }

    /**
     * Return filename
     *
     * @param \XLite\Module\CDev\FileAttachments\Model\Product\Attachment $attachment
     *
     * @return string
     */
    protected function getFileLink(\XLite\Module\CDev\FileAttachments\Model\Product\Attachment $attachment)
    {
        return $attachment->getStorage()->getAdminGetterURL();
    }

    /**
     * Define repository name
     *
     * @return string
     */
    protected function defineRepositoryName()
    {
        return 'XLite\Module\CDev\FileAttachments\Model\Product\Attachment';
    }

    /**
     * Return class name for the list pager
     *
     * @return string
     */
    protected function getPagerClass()
    {
        return 'XLite\View\Pager\Admin\Model\Infinity';
    }

    /**
     * Mark list as sortable
     *
     * @return integer
     */
    protected function getSortableType()
    {
        return static::SORT_TYPE_MOVE;
    }

    /**
     * Mark list as switchable (enable / disable)
     *
     * @return boolean
     */
    protected function isDisplayWithEmptyList()
    {
        return true;
    }

    /**
     * Mark list as removable
     *
     * @return boolean
     */
    protected function isRemoved()
    {
        return true;
    }

    /**
     * Get top actions
     *
     * @return array
     */
    protected function getTopActions()
    {
        $actions = parent::getTopActions();

        $actions[] = 'modules/CDev/FileAttachments/parts/add_file.twig';

        return $actions;
    }

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' product-attachments';
    }

    /**
     * Default search conditions
     *
     * @param  \XLite\Core\CommonCell $searchCase Search case
     *
     * @return \XLite\Core\CommonCell
     */
    protected function postprocessSearchCase(\XLite\Core\CommonCell $searchCase)
    {
        $searchCase = parent::postprocessSearchCase($searchCase);

        $searchCase->{\XLite\Module\CDev\FileAttachments\Model\Repo\Product\Attachment::P_PRODUCT} = $this->getProduct();

        return $searchCase;
    }

    /**
     * Get URL common parameters
     *
     * @return array
     */
    protected function getCommonParams()
    {
        $this->commonParams = parent::getCommonParams();
        $this->commonParams['product_id'] = $this->getProductId();
        $this->commonParams['page'] = 'attachments';

        return $this->commonParams;
    }

    /**
     * getSortByModeDefault
     *
     * @return string
     */
    protected function getSortByModeDefault()
    {
        return 'a.orderby';
    }

    /**
     * Return sort field name for tag
     *
     * @return string
     */
    protected function getSortFieldName()
    {
        return 'orderby';
    }

    /**
     * Define line class as list of names
     *
     * @param integer              $index  Line index
     * @param \XLite\Model\AEntity $entity Line model OPTIONAL
     *
     * @return array
     */
    protected function defineLineClass($index, \XLite\Model\AEntity $entity = null)
    {
        $classes = parent::defineLineClass($index, $entity);

        if ($entity && $entity->getStorage()) {
            switch ($entity->getStorage()->getStorageType()) {
                case \XLite\Model\Base\Storage::STORAGE_URL:
                    $classes[] = 'storage-url';
                    break;
                case \XLite\Model\Base\Storage::STORAGE_ABSOLUTE:
                case \XLite\Model\Base\Storage::STORAGE_RELATIVE:
                    $classes[] = 'storage-local';
                    break;
            }
        }

        return $classes;
    }

    /**
     * Get right actions templates name
     *
     * @return array
     */
    protected function getRightActions()
    {
        $actions = [
            'modules/CDev/FileAttachments/parts/action.download.twig',
            'modules/CDev/FileAttachments/parts/action.reupload.twig'
        ];

        return array_merge($actions, parent::getRightActions());
    }
}