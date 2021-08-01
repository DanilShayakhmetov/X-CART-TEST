<?php
namespace XLite\Model;
/**
 * Category
 *
 * @Entity
 * @Table (name="category_products",
 *      uniqueConstraints={
 *          @UniqueConstraint (name="pair", columns={"category_id","product_id"})
 *      },
 *      indexes={
 *          @Index (name="orderby", columns={"orderby"}),
 *          @Index (name="orderbyInProduct", columns={"orderbyInProduct"})
 *      }
 * )
 */
class CategoryProducts extends \XLite\Module\QSL\CloudSearch\Model\IndexingEventTriggers\CategoryProducts {}