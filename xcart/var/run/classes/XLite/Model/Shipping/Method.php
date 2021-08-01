<?php
namespace XLite\Model\Shipping;
/**
 * Shipping method model
 *
 * @Entity
 * @Table  (name="shipping_methods",
 *      indexes={
 *          @Index (name="processor", columns={"processor"}),
 *          @Index (name="carrier", columns={"carrier"}),
 *          @Index (name="enabled", columns={"enabled"}),
 *          @Index (name="position", columns={"position"})
 *      }
 * )
 */
class Method extends \XLite\Module\XC\CanadaPost\Model\Shipping\Method {}