<?php
namespace XLite\Model\Product;
/**
 * The "tab" model class
 *
 * @Entity
 * @Table  (name="global_product_tabs",
 *      uniqueConstraints={
 *          @UniqueConstraint (name="service_name", columns={"service_name"})
 *      }
 * )
 */
class GlobalTab extends \XLite\Module\XC\CustomProductTabs\Model\Product\GlobalTab {}