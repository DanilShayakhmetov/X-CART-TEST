<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Mail\Common;


use XLite\Core\Converter;
use XLite\Core\Database;
use XLite\Core\Mailer;
use XLite\Model\Product;

class LowLimitAdmin extends \XLite\Core\Mail\AMail
{
    public static function getInterface()
    {
        return \XLite::ADMIN_INTERFACE;
    }

    public static function getDir()
    {
        return 'low_limit_warning';
    }

    public static function productUrlProcessor(Product $product)
    {
        return Converter::buildFullURL(
            'product',
            '',
            [
                'product_id' => $product->getId(),
                'page'       => 'inventory',
            ],
            \XLite::getAdminScript()
        );
    }

    protected static function defineVariables()
    {
        return [
                'product_name'     => static::t('Product name'),
                'latest_sale_date' => Converter::formatDate(),
                'product_qty'      => 1,
            ] + parent::defineVariables();
    }

    public function __construct(array $productData)
    {
        parent::__construct();

        $this->setFrom(Mailer::getOrdersDepartmentMail());
        $this->setTo(Mailer::getSiteAdministratorMails());
        $this->setReplyTo(Mailer::getOrdersDepartmentMails());

        $this->populateVariables([
            'product_name'     => $productData['name'],
            'latest_sale_date' => Converter::formatDate($this->getLatestSaleDate($productData['product'])),
            'product_qty'      => $this->getProductStock($productData['product']),
        ]);

        $this->appendData([
            'product'               => $productData['product'],
            'amount'                => $productData['amount'],
            'product_url_processor' => [static::class, 'productUrlProcessor'],
        ]);
    }

    protected function getLatestSaleDate(Product $product)
    {
        $qb = Database::getRepo('XLite\Model\OrderItem')->createPureQueryBuilder('oi');
        $alias = $qb->getMainAlias();

        $qb->select('o.date')
            ->linkInner("$alias.order", 'o')
            ->orderBy('o.date', 'DESC')
            ->andWhere("$alias.object = :product")
            ->setParameter('product', $product)
            ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleScalarResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }

    protected function getProductStock(Product $product)
    {
        return $product->getQty();
    }

    /**
     * Unique hash each product (same class cant send more than once)
     * TODO: remove on implement multiple products in one mail
     *
     * @return array
     */
    protected function getHashData()
    {
        return array_merge(parent::getHashData(), [$this->getVariable('product_name')]);
    }
}