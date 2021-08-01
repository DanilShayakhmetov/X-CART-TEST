<?php
namespace XLite\Model;
/**
 * View list
 *
 * @Entity
 * @Table  (name="view_lists",
 *          indexes={
 *              @Index (name="tl", columns={"tpl", "list"}),
 *              @Index (name="lzv", columns={"list", "zone", "version"}),
 *              @Index (name="tclz", columns={"tpl", "child", "list", "zone"})
 *          }
 * )
 * @HasLifecycleCallbacks
 */
class ViewList extends \XLite\Module\XC\ThemeTweaker\Model\ViewList {}