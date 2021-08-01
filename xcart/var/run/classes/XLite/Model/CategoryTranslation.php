<?php
namespace XLite\Model;
/**
 * Category multilingual data
 *
 * @Entity
 * @Table  (name="category_translations",
 *      indexes={
 *          @Index (name="ci", columns={"code","id"}),
 *          @Index (name="id", columns={"id"})
 *      }
 * )
 */
class CategoryTranslation extends \XLite\Module\CDev\GoSocial\Model\CategoryTranslation {}