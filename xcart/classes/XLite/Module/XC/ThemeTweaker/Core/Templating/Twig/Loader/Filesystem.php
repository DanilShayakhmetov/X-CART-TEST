<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\Core\Templating\Twig\Loader;


class Filesystem extends \XLite\Core\Templating\Twig\Loader\Filesystem implements \XLite\Base\IDecorator
{
    protected function findTemplate($name)
    {
        $result = call_user_func_array(array('parent', __FUNCTION__), func_get_args());

        if (
            $result
            && \XLite\Core\Layout::getInstance()->isDisabledTemplate($name)
        ) {
            $name = $this->normalizeName($name);
            list($namespace, $shortname) = $this->parseName($name);

            $actual = false;

            foreach ($this->paths[$namespace] as $path) {
                if (
                    strpos($path, 'theme_tweaker') === false
                    && is_file($path.'/'.$shortname)
                ) {
                    if (false !== $realpath = realpath($path.'/'.$shortname)) {
                        $actual = $realpath;
                        break;
                    }

                    $actual = $path.'/'.$shortname;
                    break;
                }
            }

            return $this->cache[$name] = $actual;
        }

        return $result;
    }
}