<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\PINCodes\Controller\Admin;

use XLite\Core\Database;
use XLite\Core\Request;

/**
 * Product modify
 *
 */
 class Product extends \XLite\Module\XC\Concierge\Controller\Admin\Product implements \XLite\Base\IDecorator
{
    /**
     * Update pin codes action handler
     *
     * @return void
     */
    public function doActionUpdatePinCodes()
    {
        $product = $this->getProduct();

        $product->setPinCodesEnabled((bool)Request::getInstance()->pins_enabled);
        $product->setAutoPinCodes(Request::getInstance()->autoPinCodes);

        if (Request::getInstance()->delete) {
            foreach (Request::getInstance()->delete as $id => $checked) {
                $obj = Database::getRepo('XLite\Module\CDev\PINCodes\Model\PinCode')->findOneBy(
                    [
                        'id'      => $id,
                        'product' => $product->getId(),
                        'isSold'  => 0
                    ]
                );
                if ($obj) {
                    Database::getEM()->remove($obj);
                }
            }
        }

        Database::getEM()->flush($product);
        if ($product->hasManualPinCodes()) {
            $product->syncAmount();
            $product->setInventoryEnabled(true);
        }
        Database::getEM()->flush();

        \XLite\Core\TopMessage::addInfo('PIN codes data have been successfully updated');
    }

    /**
     * Get pages sections
     *
     * @return array
     */
    public function getPages()
    {
        $pages = parent::getPages();
        if (!$this->isNew()) {
            $pages['pin_codes'] = static::t('PIN codes');
        }

        return $pages;
    }

    /**
     * Get pages templates
     *
     * @return array
     */
    protected function getPageTemplates()
    {
        $templates = parent::getPageTemplates();

        if (!$this->isNew()) {
            $templates += [
                'pin_codes' => 'modules/CDev/PINCodes/product/pin_codes.twig',
            ];
        }

        return $templates;
    }
}
