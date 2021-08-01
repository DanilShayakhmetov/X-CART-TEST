/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * editable language popover template
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

var EditableLabelTemplate =
    '<textarea class="xlite-translation-wrapper">' +
        '<%= translation %>' +
    '</textarea>' +
    '<span class="xlite-translation-help"><%= core.t("xlite-translation-popover.help") %></span>' +
    '<div class="xlite-translation-buttons">' +
        '<button class="xlite-translation-button themetweaker-button" data-action="discard">Cancel</button>' +
        '<button class="xlite-translation-button themetweaker-main-button" data-action="save">Save</button>' +
    '</div>';