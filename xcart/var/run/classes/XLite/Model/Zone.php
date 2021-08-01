<?php
namespace XLite\Model;
/**
 * Zone model
 *
 * @Entity
 * @Table  (name="zones",
 *      indexes={
 *          @Index (name="zone_name", columns={"zone_name"}),
 *          @Index (name="zone_default", columns={"is_default"})
 *      }
 * )
 */
class Zone extends \XLite\Module\CDev\Coupons\Model\Zone {}