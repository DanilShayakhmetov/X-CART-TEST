<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

return function()
{
    $update = updateEmailNotifications5401();

    if ($update) {
        \XLite\Core\Database::getEM()->flush();
    }
};

function updateEmailNotifications5401()
{
    $result = false;

    $translations = \XLite\Core\Database::getRepo('XLite\Model\NotificationTranslation')->findAll();

    // Replace service variable %notification_body% -> %dynamic_message%
    foreach ($translations as $t) {
        foreach (['AdminText', 'CustomerText'] as $name) {
            if (preg_match('/%notification_body%/', ($value = $t->{'get' . $name}()))) {
                $value = preg_replace('/%notification_body%/', '%dynamic_message%', $value);
                $t->{'set' . $name}($value);
                $result = true;
            }
        }
    }

    return $result;
}
