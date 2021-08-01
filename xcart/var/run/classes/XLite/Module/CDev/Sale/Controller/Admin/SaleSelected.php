<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Sale\Controller\Admin;

/**
 * Sale selected controller
 */
class SaleSelected extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Set the sale price');
    }

    /**
     * Set sale price parameters for products list
     *
     * @return void
     */
    protected function doActionSetSalePrice()
    {
        $form = new \XLite\Module\CDev\Sale\View\Form\SaleSelectedDialog();
        $form->getRequestData();

        if ($form->getValidationMessage()) {
            \XLite\Core\TopMessage::addError($form->getValidationMessage());

        } elseif ($this->getSelected()) {
            \XLite\Core\Database::getRepo('\XLite\Model\Product')->updateInBatchById($this->getUpdateInfo());
            \XLite\Core\TopMessage::addInfo('Products information has been successfully updated');

            \XLite\Core\Event::ProductsListMassUncheck();

        } elseif ($ids = $this->getActionProductsIds()) {
            $qb = \XLite\Core\Database::getRepo('XLite\Model\Product')->createQueryBuilder();
            $alias = $qb->getMainAlias();
            $qb->update('\XLite\Model\Product', $alias)
                ->andWhere($qb->expr()->in("{$alias}.product_id", $ids));

            foreach ($this->getUpdateInfoElement() as $key => $value) {
                $qb->set("{$alias}.{$key}", ":{$key}")
                    ->setParameter($key, $value);
            }

            $qb->execute();

            \XLite\Core\TopMessage::addInfo('Products information has been successfully updated');
        }

        $this->setReturnURL($this->buildURL('product_list', '', ['mode' => 'search']));
    }

    /**
     * @return array
     */
    protected function getActionProductsIds()
    {
        $cnd = \XLite\Controller\Admin\ProductList::getInstance()->getItemsList()
            ->getActionSearchCondition();
        $ids = \XLite\Core\Database::getRepo('XLite\Model\Product')
            ->search($cnd, \XLite\Model\Repo\ARepo::SEARCH_MODE_IDS);
        $ids = is_array($ids) ? array_unique(array_filter($ids)) : [];

        return $ids;
    }

    /**
     * Return result array to update in batch list of products
     *
     * @return array
     */
    protected function getUpdateInfo()
    {
        return array_fill_keys(
            array_keys($this->getSelected()),
            $this->getUpdateInfoElement()
        );
    }

    /**
     * Return one element to update.
     *
     * @return array
     */
    protected function getUpdateInfoElement()
    {
        $data = $this->getPostedData();

        return [
            'participateSale' => (
                0 !== $data['salePriceValue']
                || \XLite\Model\Product::SALE_DISCOUNT_TYPE_PERCENT !== $data['discountType']
            )
        ] + $data;
    }
}
