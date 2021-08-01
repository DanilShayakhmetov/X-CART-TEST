<?php
namespace XLite\Model;
/**
 * Something customer can put into his cart
 *
 * @Entity
 * @Table  (name="order_items",
 *          indexes={
 *               @Index (name="ooo", columns={"order_id","object_type","object_id"}),
 *               @Index (name="object_id", columns={"object_id"}),
 *               @Index (name="price", columns={"price"}),
 *               @Index (name="amount", columns={"amount"})
 *          }
 * )
 *
 * @InheritanceType       ("SINGLE_TABLE")
 * @DiscriminatorColumn   (name="object_type", type="string", length=16)
 * @DiscriminatorMap      ({"product" = "XLite\Model\OrderItem"})
 * @HasLifecycleCallbacks
 */
class OrderItem extends \XLite\Module\CDev\Egoods\Model\OrderItem {}