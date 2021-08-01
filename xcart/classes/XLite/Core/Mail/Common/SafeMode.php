<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Mail\Common;


use XLite\Core\Mailer;

class SafeMode extends \XLite\Core\Mail\AMail
{
    static function getInterface()
    {
        return \XLite::ADMIN_INTERFACE;
    }

    static function getDir()
    {
        return 'safe_mode_key_generated';
    }

    public function __construct($key, $changed = false)
    {
        parent::__construct();

        $this->setFrom(Mailer::getSiteAdministratorMail());
        $this->setTo(Mailer::getSiteAdministratorMails());

        $this->appendData([
            'key'                  => $key,
            'keyChanged'           => $changed,
            'current_snapshot_url' => \Includes\SafeMode::getLatestSnapshotURL(),
            'hard_reset_url'       => \Includes\SafeMode::getResetURL(),
            'soft_reset_url'       => \Includes\SafeMode::getResetURL(\Includes\SafeMode::MODE_SOFT),
            'article_url'          => \XLite::getController()->getArticleURL(),
        ]);
    }
}