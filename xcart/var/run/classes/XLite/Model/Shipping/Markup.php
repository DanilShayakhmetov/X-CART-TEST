<?php
namespace XLite\Model\Shipping;
/**
 * Shipping markup model
 *
 * @Entity
 * @Table (name="shipping_markups",
 *      indexes={
 *          @Index (name="rate", columns={"method_id","zone_id","min_weight","min_total","min_discounted_total","min_items"}),
 *          @Index (name="max_weight", columns={"max_weight"}),
 *          @Index (name="max_total", columns={"max_total"}),
 *          @Index (name="max_discounted_total", columns={"max_discounted_total"}),
 *          @Index (name="max_items", columns={"max_items"}),
 *          @Index (name="markup_flat", columns={"markup_flat"}),
 *          @Index (name="markup_per_item", columns={"markup_per_item"}),
 *          @Index (name="markup_percent", columns={"markup_percent"}),
 *          @Index (name="markup_per_weight", columns={"markup_per_weight"})
 *      }
 * )
 */
class Markup extends \XLite\Module\XC\FreeShipping\Model\Shipping\Markup {}