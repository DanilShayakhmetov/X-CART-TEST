<?php
namespace XLite\Model;
/**
 * CleanURL
 *
 * @Entity
 * @Table (name="clean_urls",
 *      indexes={
 *          @Index (name="cleanURL", columns={"cleanURL"}),
 *      }
 * )
 */
class CleanURL extends \XLite\Module\CDev\Sale\Model\CleanURL {}