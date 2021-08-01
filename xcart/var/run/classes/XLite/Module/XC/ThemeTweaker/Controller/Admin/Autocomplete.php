<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\Controller\Admin;


use XLite\Model\Order;
use XLite\Model\Product;
use XLite\Model\Profile;

 class Autocomplete extends \XLite\Controller\Admin\AutocompleteAbstract implements \XLite\Base\IDecorator
{
    /**
     * @param $term
     *
     * @return array
     * @throws \Doctrine\ORM\ORMException
     */
    protected function assembleDictionaryThemeTweakerProfile($term)
    {
        $data = array_map(
            function (Profile $profile) {
                return $profile->getLogin();
            },
            \XLite\Core\Database::getRepo('\XLite\Model\Profile')
                ->findProfilesByTerm($term, 5)
        );

        return array_combine($data, $data);
    }

    /**
     * @param $term
     *
     * @return array
     * @throws \Doctrine\ORM\ORMException
     */
    protected function assembleDictionaryThemeTweakerProduct($term)
    {
        $data = array_map(
            function (Product $product) {
                return $product->getSku();
            },
            \XLite\Core\Database::getRepo('\XLite\Model\Product')
                ->findProductsByTerm($term, 5)
        );

        return array_combine($data, $data);
    }

    /**
     * @param $term
     *
     * @return array
     * @throws \Doctrine\ORM\ORMException
     */
    protected function assembleDictionaryThemeTweakerOrder($term)
    {
        $data = array_map(
            function (Order $order) {
                return $order->getOrderNumber();
            },
            \XLite\Core\Database::getRepo('\XLite\Model\Order')
                ->findOrdersByTerm($term, 5)
        );

        return array_combine($data, $data);
    }
}