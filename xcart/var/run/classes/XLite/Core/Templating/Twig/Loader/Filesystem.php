<?php
namespace XLite\Core\Templating\Twig\Loader;
/**
 * Custom Filesystem loader that exposes findTemplate publicly as getTemplatePath
 * (for ex. to be used in diagnostic purposes when loading templates)
 */
class Filesystem extends \XLite\Module\XC\ThemeTweaker\Core\Templating\Twig\Loader\Filesystem {}