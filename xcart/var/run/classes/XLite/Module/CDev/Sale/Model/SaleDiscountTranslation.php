<?php
namespace XLite\Module\CDev\Sale\Model;
/**
 * Shipping method multilingual data
 *
 * @Entity
 * @Table (name="sale_discount_translations")
 *      indexes={
 *          @Index (name="ci", columns={"code","id"}),
 *          @Index (name="id", columns={"id"})
 *      }
 * )
 */
class SaleDiscountTranslation extends \XLite\Module\CDev\GoSocial\Model\SaleDiscountTranslation {}