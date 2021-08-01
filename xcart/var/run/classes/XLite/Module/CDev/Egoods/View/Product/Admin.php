<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Egoods\View\Product;

/**
 * Product attachments tab
 */
 class Admin extends \XLite\Module\CDev\FileAttachments\View\Product\AdminAbstract implements \XLite\Base\IDecorator
{
    /**
     * @return array
     */
    protected function defineTabs()
    {
        return parent::defineTabs() + [
            'history'     => [
                'weight'   => 200,
                'title'    => static::t('History of downloads'),
                'template' => 'modules/CDev/Egoods/product/history.twig',
            ],
        ];
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/CDev/Egoods/product/style.css';

        return $list;
    }

    /**
     * Get item class
     *
     * @param \XLite\Module\CDev\FileAttachments\Model\Product\Attachment $attachment Attachment
     *
     * @return string
     */
    protected function getItemClass(\XLite\Module\CDev\FileAttachments\Model\Product\Attachment $attachment)
    {
        $class = explode(' ', parent::getItemClass($attachment));

        if ($attachment->getPrivate()) {
            $class[] = 'private';
        }
        
        return implode(' ', $class);
    }
}

