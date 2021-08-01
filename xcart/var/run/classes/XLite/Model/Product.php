<?php
namespace XLite\Model;
/**
 * The "product" model class
 *
 * @Entity
 * @Table  (name="products",
 *      indexes={
 *          @Index (name="sku", columns={"sku"}),
 *          @Index (name="price", columns={"price"}),
 *          @Index (name="weight", columns={"weight"}),
 *          @Index (name="free_shipping", columns={"free_shipping"}),
 *          @Index (name="customerArea", columns={"enabled","arrivalDate"})
 *      }
 * )
 * @HasLifecycleCallbacks
 */
class Product extends \XLite\Module\CDev\Coupons\Model\Product {}