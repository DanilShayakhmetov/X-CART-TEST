<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\VolumeDiscounts\Controller\Admin;

/**
 * Volume discount
 */
class VolumeDiscount extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Controller parameters
     *
     * @var   array
     */
    protected $param = array('target', 'id');

    /**
     * Volume discount id
     *
     * @var   integer
     */
    protected $id;

    /**
     * Check ACL permissions
     *
     * @return boolean
     */
    public function checkACL()
    {
        return parent::checkACL() ||
            \XLite\Core\Auth::getInstance()->isPermissionAllowed('manage volume discounts');
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Volume discount');
    }

    /**
     * Update volume discount
     *
     * @return void
     */
    public function doActionUpdate()
    {
        $this->getModelForm()->performAction('modify');

        if ($this->getModelForm()->isValid()) {
            $this->setReturnURL(
                \XLite\Core\Converter::buildURL(
                    'promotions',
                    '',
                    ['page' => \XLite\Controller\Admin\Promotions::PAGE_VOLUME_DISCOUNTS]
                )
            );
        }
    }

    /**
     * Returns volume discount
     *
     * @return \XLite\Module\CDev\VolumeDiscounts\Model\VolumeDiscount
     */
    protected function getVolumeDiscount()
    {
        return $this->getModelForm()->getModelObject();
    }

    /**
     * Get model form class
     *
     * @return string
     */
    protected function getModelFormClass()
    {
        return 'XLite\Module\CDev\VolumeDiscounts\View\Model\VolumeDiscount';
    }
}
