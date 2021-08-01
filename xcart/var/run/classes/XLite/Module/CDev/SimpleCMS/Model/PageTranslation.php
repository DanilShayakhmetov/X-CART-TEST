<?php
namespace XLite\Module\CDev\SimpleCMS\Model;
/**
 * Page
 *
 * @Entity
 * @Table (name="page_translations",
 *      indexes={
 *          @Index (name="ci", columns={"code","id"}),
 *          @Index (name="id", columns={"id"})
 *      }
 * )
 */
class PageTranslation extends \XLite\Module\CDev\GoSocial\Model\PageTranslation {}