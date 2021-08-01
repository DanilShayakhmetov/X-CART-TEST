<?php
namespace XLite\Module\CDev\FileAttachments\Model\Product;
/**
 * Product attachment
 *
 * @Entity
 * @Table  (name="product_attachments",
 *      indexes={
 *          @Index (name="o", columns={"orderby"})
 *      }
 * )
 */
class Attachment extends \XLite\Module\CDev\Egoods\Model\Product\Attachment {}