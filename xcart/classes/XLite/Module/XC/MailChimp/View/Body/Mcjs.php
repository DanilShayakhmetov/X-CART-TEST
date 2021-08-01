<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\View\Body;

use XLite\Module\XC\MailChimp\Core\MailChimp;

/**
 * @ListChild (list="body", zone="customer", weight="1999999")
 */
class Mcjs extends \XLite\View\AView
{
    /**
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/XC/MailChimp/body/parts/mcjs.twig';
    }

    /**
     * @param $trim
     * @return string
     */
    protected function getMcjsContent($trim = false)
    {
        $script = \XLite\Core\Config::getInstance()->XC->MailChimp->mcjs;

        if ($trim) {
            $script = str_replace(['<script id="mcjs">', '</script>'], '', $script);
        }

        return $script;
    }

    /**
     * @return bool
     */
    protected function isVisible()
    {
        return (boolean)\XLite\Core\Config::getInstance()->XC->MailChimp->mcjs;
    }
}