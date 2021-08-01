<?php
namespace XLite\Module\CDev\SimpleCMS\Model;
/**
 * Menu
 *
 * @Entity
 * @Table  (name="menus",
 *      indexes={
 *          @Index (name="enabled", columns={"enabled", "type"}),
 *          @Index (name="position", columns={"position"})
 *      }
 * )
 */
class Menu extends \XLite\Module\CDev\Bestsellers\Model\Menu {}