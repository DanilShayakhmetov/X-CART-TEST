<?php
namespace XLite\Model;
/**
 * Product multilingual data
 *
 * @Entity
 *
 * @Table (name="product_translations",
 *         indexes={
 *              @Index (name="ci", columns={"code","id"}),
 *              @Index (name="id", columns={"id"}),
 *              @Index (name="name", columns={"name"})
 *         }
 * )
 */
class ProductTranslation extends \XLite\Module\CDev\GoSocial\Model\ProductTranslation {}