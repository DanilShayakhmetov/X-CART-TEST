<?php
namespace XLite\Module\CDev\SimpleCMS\Model;
/**
 * Page
 *
 * @Entity
 * @Table  (name="pages",
 *      indexes={
 *          @Index (name="enabled", columns={"enabled"}),
 *      }
 * )
 */
class Page extends \XLite\Module\CDev\GoSocial\Model\Page {}