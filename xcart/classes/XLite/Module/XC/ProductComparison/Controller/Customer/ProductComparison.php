<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductComparison\Controller\Customer;

use XLite\Core\TopMessage;

/**
 * Product comparison
 */
class ProductComparison extends \XLite\Controller\Customer\ACustomer
{
    /**
     * Controller parameters
     *
     * @var array
     */
    protected $params = ['target'];

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return \XLite\Module\XC\ProductComparison\Core\Data::getInstance()->getTitle();
    }

    /**
     * Product comparison delete
     *
     * @return void
     */
    protected function doActionDelete()
    {
        $id = \XLite\Core\Request::getInstance()->product_id;
        \XLite\Module\XC\ProductComparison\Core\Data::getInstance()->deleteProductId($id);
        $this->afterAction('delete', $id);

        TopMessage::addInfo('The product has been removed from comparison table', [
            'url' => $this->buildURL('compare')
        ]);
    }

    /**
     * Product comparison add
     *
     * @return void
     */
    protected function doActionAdd()
    {
        $id = \XLite\Core\Request::getInstance()->product_id;
        \XLite\Module\XC\ProductComparison\Core\Data::getInstance()->addProductId($id);
        $this->afterAction('add', $id);

        TopMessage::addInfo('The product has been added to the comparison table', [
            'url' => $this->buildURL('compare')
        ]);
    }

    /**
     * Clear list
     *
     * @return void
     */
    protected function doActionClear()
    {
        \XLite\Module\XC\ProductComparison\Core\Data::getInstance()->clearList();
        $this->afterAction('clear');

        TopMessage::addInfo('The comparison table has been cleared.');
    }

    /**
     * After action
     *
     * @param string  $action Action
     * @param integer $id     Id OPTIONAL
     *
     * @return void
     */
    protected function afterAction($action, $id = 0)
    {
        $data = [
            'productId' => $id,
            'action'    => $action,
            'title'     => $this->getTitle(),
            'count'     => \XLite\Module\XC\ProductComparison\Core\Data::getInstance()->getProductsCount(),
        ];

        \XLite\Core\Event::updateProductComparison($data);

        ob_start();
        print json_encode($data);
    }
}
